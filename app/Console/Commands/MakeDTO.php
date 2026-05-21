<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDto extends Command
{
    protected $signature = 'make:dto
        {name : Nome da classe, aceita subpastas (ex: User ou Users/User)}
        {--r|response : Gera ResponseDTO com fromModel() — para dados de saída/resposta}
        {--A|array : Gera DTO com fromArray() — para dados de entrada em array puro (alternativa ao padrão fromRequest)}
        {--N|no-suffix : Ignora o sufixo automático DTO/ResponseDTO e usa o nome exato informado}
        {--all : Gera o par completo: UserDTO (fromRequest) + UserResponseDTO (fromModel)}';

    protected $description = 'Gera uma classe DTO. Use -r para ResponseDTO, -A para fromArray, -N para ignorar sufixos ou --all para gerar o par completo.';

    protected $help = <<<HELP
Exemplos de uso:

  DTO de entrada padrão (fromRequest):
    php artisan make:dto User

  ResponseDTO de saída (fromModel):
    php artisan make:dto User --response

  DTO com fromArray (dados brutos, sem validação de request):
    php artisan make:dto User --array

  Par completo: UserDTO + UserResponseDTO de uma só vez:
    php artisan make:dto User --all

  Em subpasta:
    php artisan make:dto Auth/Login --all

  Sem sufixo automático (nome exato):
    php artisan make:dto UserData --no-suffix
HELP;

    public function handle()
    {
        $rawName = $this->argument('name');
        $isAll = (bool) $this->option('all');
        $isResponse = (bool) $this->option('response');
        $isArray = (bool) $this->option('array');
        $noSuffix = (bool) $this->option('no-suffix');

        if ($isAll) {
            $base = $this->stripDtoSuffixes(trim($rawName, '/\\'));
            $resultDto      = $this->createDto($base . 'DTO',         false, false, true);
            $resultResponse = $this->createDto($base . 'ResponseDTO', false, false, true);

            return ($resultDto === Command::FAILURE || $resultResponse === Command::FAILURE)
                ? Command::FAILURE
                : Command::SUCCESS;
        }

        if ($isResponse && $isArray) {
            $this->error('A flag -A (--array) serve apenas para dados de entrada e não pode ser usada junto com -r (--response).');
            return Command::FAILURE;
        }

        return $this->createDto($rawName, $isResponse, $isArray, $noSuffix);
    }

    private function stripDtoSuffixes(string $name): string
    {
        $lower = strtolower($name);

        foreach (['responsedto', 'dto', 'response'] as $suffix) {
            if (Str::endsWith($lower, $suffix)) {
                return substr($name, 0, -strlen($suffix));
            }
        }

        return $name;
    }

    private function createDto(string $name, bool $isResponse, bool $isArray, bool $noSuffix): int
    {
        $name = trim($name, '/\\');

        if (!$noSuffix) {
            if ($isResponse) {
                if (Str::endsWith(strtolower($name), 'dto')) {
                    $name = substr($name, 0, -3);
                }
                if (Str::endsWith(strtolower($name), 'response')) {
                    $name = substr($name, 0, -8);
                }
                $name .= 'ResponseDTO';
            } else {
                if (!Str::endsWith(strtolower($name), 'dto')) {
                    $name .= 'DTO';
                } else {
                    $name = substr($name, 0, -3) . 'DTO';
                }
            }
        }

        $directory = app_path('DTOs');
        $namespace = 'App\DTOs';
        $cleanName = str_replace('\\', '/', $name);
        $path = $directory . '/' . $cleanName . '.php';
        $className = class_basename($name);

        $subNamespace = str_replace('/', '\\', dirname($cleanName));
        if ($subNamespace !== '.') {
            $namespace .= '\\' . $subNamespace;
        }

        if (File::exists($path)) {
            $this->error("A classe [{$name}] já existe!");
            return Command::FAILURE;
        }

        File::ensureDirectoryExists(dirname($path));

        if ($isResponse) {
            $content = $this->getResponseStub($namespace, $className);
        } elseif ($isArray) {
            $content = $this->getArrayStub($namespace, $className);
        } else {
            $content = $this->getRequestStub($namespace, $className);
        }

        File::put($path, $content);
        $this->info("Classe [{$className}] gerada com sucesso em: {$path}");

        return Command::SUCCESS;
    }

    private function getRequestStub(string $namespace, string $className): string
    {
        return <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    public function __construct(
        // public readonly string \$exemplo,
    ) {}

    public static function fromRequest(array \$data): self
    {
        return new self(
            // exemplo: \$data['exemplo'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            // 'exemplo' => \$this->exemplo,
        ];
    }
}

PHP;
    }

    private function getResponseStub(string $namespace, string $className): string
    {
        return <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Eloquent\Model;

class {$className}
{
    public function __construct(
        // public readonly string \$exemplo,
    ) {}

    public static function fromModel(Model \$model): self
    {
        return new self(
            // exemplo: \$model->exemplo,
        );
    }

    public function toArray(): array
    {
        return [
            // 'exemplo' => \$this->exemplo,
        ];
    }
}

PHP;
    }

    private function getArrayStub(string $namespace, string $className): string
    {
        return <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    public function __construct(
        // public readonly string \$exemplo,
    ) {}

    public static function fromArray(array \$data): self
    {
        return new self(
            // exemplo: \$data['exemplo'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            // 'exemplo' => \$this->exemplo,
        ];
    }
}

PHP;
    }
}
