<?php

declare(strict_types=1);

namespace App\Boost\Agents;

use Laravel\Boost\Contracts\SupportsGuidelines;
use Laravel\Boost\Contracts\SupportsMcp;
use Laravel\Boost\Contracts\SupportsSkills;
use Laravel\Boost\Install\Agents\Agent;
use Laravel\Boost\Install\Enums\Platform;

class CustomAgent extends Agent implements SupportsGuidelines, SupportsMcp, SupportsSkills
{
    public function name(): string
    {
        return 'zoo_code';
    }

    public function displayName(): string
    {
        return 'Zoo Code';
    }

    public function systemDetectionConfig(Platform $platform): array
    {
        return match ($platform) {
            Platform::Darwin => [
                'paths' => ['/Applications/Visual Studio Code.app'],
            ],
            Platform::Linux => [
                'command' => 'command -v code',
            ],
            Platform::Windows => [
                'paths' => [
                    '%APPDATA%\\Code\\User\\globalStorage\\zoocodeorganization.zoo-code',
                    '%ProgramFiles%\\Microsoft VS Code',
                    '%LOCALAPPDATA%\\Programs\\Microsoft VS Code',
                ],
            ],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'paths' => ['.roo'],
            'files' => ['.roo/mcp.json', 'AGENTS.md'],
        ];
    }

    public function mcpConfigPath(): string
    {
        return config('boost.agents.zoo_code.mcp_config_path', '.roo/mcp.json');
    }

    public function guidelinesPath(): string
    {
        return config('boost.agents.zoo_code.guidelines_path', 'AGENTS.md');
    }

    public function skillsPath(): string
    {
        return config('boost.agents.zoo_code.skills_path', '.agents/skills');
    }

    /**
     * ponytail: include cwd+autoApprove so boost:install writes full Zoo Code config, upgrade when upstream supports zoo_code natively
     *
     * @param  array<int, string>  $args
     * @param  array<string, string>  $env
     * @return array<string, mixed>
     */
    public function mcpServerConfig(string $command, array $args = [], array $env = []): array
    {
        $config = [
            'command' => $command,
            'args' => $args,
            'cwd' => base_path(),
            'disabled' => false,
            'autoApprove' => [
                'application_info',
                'get_absolute_url',
                'database_schema',
                'search_docs',
                'database_query',
                'browser_logs',
                'last_error',
                'read_log_entries',
                'list_routes',
                'tinker',
            ],
        ];

        if ($env !== []) {
            $config['env'] = $env;
        }

        return $config;
    }
}
