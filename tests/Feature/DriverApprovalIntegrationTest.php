<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Infrastructure\Models\Driver as DriverEloquentModel;
use App\Domain\Interfaces\IDriverRepository;
use Mockery;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DriverApprovalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cenário 1 (Sucesso): Uma requisição POST para '/api/v1/drivers/{driverId}/approve'
     * com um ID válido e status 'pending' (verificação pendente) deve retornar status HTTP 200
     * e alterar o status no banco para 'approved'.
     */
    public function test_driver_approval_success(): void
    {
        $driverId = 'driver-123';
        
        DriverEloquentModel::create([
            'id' => $driverId,
            'status' => 'pending',
            'document_verified_at' => null
        ]);

        $response = $this->postJson("/api/v1/drivers/{$driverId}/approve");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Driver successfully approved']);

        $this->assertDatabaseHas('drivers', [
            'id' => $driverId,
            'status' => 'approved',
        ]);
        
        $driver = DriverEloquentModel::find($driverId);
        $this->assertNotNull($driver->document_verified_at);
    }

    /**
     * Cenário 2 (409 Conflict - Race Condition): Simule duas chamadas sequenciais para o método do repositório
     * 'updateStatusSafe' utilizando o mesmo ID e o mesmo status inicial 'pending'.
     * A primeira deve passar (retornar a entidade) e a segunda deve falhar (retornar null),
     * resultando em uma resposta HTTP 409 (ConflictHttpException) para a segunda chamada simulada.
     */
    public function test_driver_approval_race_condition_returns_409(): void
    {
        $driverId = 'driver-conflict-123';

        DriverEloquentModel::create([
            'id' => $driverId,
            'status' => 'pending',
            'document_verified_at' => null
        ]);

        // Vamos obter a instância do repository vinculada ao container
        $repository = app(IDriverRepository::class);

        // Primeira chamada (Sucesso): status transiciona de 'pending' para 'approved'
        $firstUpdate = $repository->updateStatusSafe($driverId, 'pending', 'approved', [
            'document_verified_at' => now()->toDateTimeString()
        ]);
        $this->assertNotNull($firstUpdate);
        $this->assertEquals('approved', $firstUpdate->status);

        // Segunda chamada (Falha - Concorrência): tenta transicionar do status inicial 'pending' (que já foi alterado para 'approved')
        $secondUpdate = $repository->updateStatusSafe($driverId, 'pending', 'approved', [
            'document_verified_at' => now()->toDateTimeString()
        ]);
        $this->assertNull($secondUpdate);

        // Agora simulamos a requisição HTTP que aciona a falha de concorrência:
        // Como o status já é 'approved', tentar aprovar novamente via HTTP causará ConflictHttpException (HTTP 409)
        $response = $this->postJson("/api/v1/drivers/{$driverId}/approve");
        $response->assertStatus(409);
    }

    /**
     * Cenário 3 (404 Not Found) ou 409: Se o ID não existe, updateStatusSafe retorna null e lança ConflictHttpException 409.
     * Como solicitado no Cenário 3: Validar que um driverId inexistente trate o fluxo sem quebrar o servidor.
     */
    public function test_driver_approval_non_existent_driver(): void
    {
        $response = $this->postJson("/api/v1/drivers/non-existent-id/approve");
        
        // Conforme a implementação do DriverApprovalService, se updateStatusSafe retorna null, lança ConflictHttpException (409)
        // Isso trata o fluxo adequadamente retornando 409 em vez de um erro 500 do servidor.
        $response->assertStatus(409);
    }
}
