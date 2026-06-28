# Velo Identity Driver Service

O `identity-driver-service` é um microserviço da Velo responsável por gerenciar a identidade e a validação de motoristas parceiros. Este serviço controla estados críticos (aprovação, rejeição, suspensão) e valida documentos, garantindo segurança transacional.

## 🏗️ Arquitetura Tridimensional (3D)

O projeto adota uma arquitetura em camadas focada no isolamento do Domínio. Isso previne o acoplamento excessivo com frameworks de banco de dados (como o ORM do Laravel) e garante que as regras de negócio sejam testáveis de forma unitária e independente.

As camadas principais são:

1. **Application (Aplicação):**
   - Lida com o tráfego externo (HTTP, Console, Eventos).
   - Diretórios: `app/Application/Http/Controllers`, `app/Application/DTOs`
   - O papel desta camada é validar a entrada, passar para o serviço de domínio apropriado e formatar a resposta.

2. **Domain (Domínio):**
   - Coração da aplicação. Contém apenas código PHP puro, totalmente independente de Eloquent ou Frameworks externos.
   - Diretórios: `app/Domain/Entities`, `app/Domain/Services`, `app/Domain/Interfaces`
   - **Exemplo**: O serviço `DriverApprovalService` orquestra as mudanças de estado, enquanto o contrato `IDriverRepository` define como os dados são lidos e gravados de forma segura.

3. **Infrastructure (Infraestrutura):**
   - Implementa detalhes técnicos e interações com o mundo externo, como banco de dados (Eloquent), filas, ou APIs externas.
   - Diretórios: `app/Infrastructure/Models`, `app/Infrastructure/Repositories`
   - Os repositórios concretos implementam as interfaces definidas no Domínio e realizam o mapeamento dos Models do banco para Entidades Puras.

## 🔒 Prevenção de Concorrência (Race Conditions)

Operações críticas de validação de motoristas são protegidas contra concorrência por meio de **Update Atômico (Lock Otimista)**. A abordagem é implementada no método `updateStatusSafe` da infraestrutura:

1. A atualização do banco usa query conditions estritas `where('status', $oldStatus)`.
2. Isso evita que dois processos modifiquem simultaneamente o status de um motorista sem saber da alteração um do outro.
3. Se houver falha na alteração, o Serviço de Domínio emite um `ConflictHttpException` (HTTP 409 Conflict), evitando estados corrompidos.

## 🚀 Instalação e Configuração

Certifique-se de que as dependências estejam instaladas e de atualizar o mapeamento de classes do composer:

```bash
# Instalar dependências
composer install

# Atualizar autoload para os novos namespaces (app/Domain, app/Infrastructure, etc)
composer dump-autoload

# Configurar o ambiente
cp .env.example .env
php artisan key:generate

# Rodar migrations (se aplicável)
php artisan migrate
```

## 🧪 Padrões de Qualidade

1. **Dependência Invertida**: Modelos Eloquent (`App\Infrastructure\Models`) nunca são instanciados ou mencionados na pasta `app/Domain`.
2. **Entidades Imutáveis**: Entidades de domínio priorizam propriedades `readonly` para assegurar que modificações de estado só aconteçam de forma declarativa e controlada.
