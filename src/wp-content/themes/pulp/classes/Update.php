<?php

namespace Pulp;

/**
 * EP Core and Plugin Updater
 *
 * --exclude=<plugins> : Comma separated list of plugin slugs to exclude
 *
 * --dry-run : Run the command without making any changes
 * --slugs : List plugin slugs eligible for updates
 *
 * ## EXAMPLES
 * // php wp-cli.phar ep-list-plugin-slugs
 * // php wp-cli.phar ep-wp-update --dry-run
 * // php wp-cli.phar ep-wp-update [--exclude=plugin-slug,plugin-slug] [--dry-run]
 */

class Update
{
    public $skipped = [];
    public $exclude = [];
    public $dryRun;

    public function __invoke($args, $assoc_args)
    {
        $this->exclude = isset($assoc_args['exclude']) ? explode(',', $assoc_args['exclude']) : [];
        $this->dryRun = isset($assoc_args['dry-run']) ? true : false;
        $this->run();
    }

    /*
    * Run updates and commit changes
    */
    public function run()
    {
        if ($this->dryRun) {
            \WP_CLI::warning('Dry run, no changes will be committed.');
        } else {
            \WP_CLI::confirm('This will update all plugins and WP core and commit the changes. You may want to back up your database too. Proceed?', $confirmArgs = []);
        }

        $this->updateCore();
        $this->updatePlugins();
        $this->updateCli();
        $this->showSkipped();

        $message = $this->dryRun ? 'Simulated updates complete!' : 'Updates complete!';
        \WP_CLI::success($message);
    }

    private function showSkipped(): void
    {
        if (!empty($this->skipped)) {
            $message = 'Skipped plugins: ' . implode(', ', $this->skipped);
            $this->outputMessage($message, 'warning');
        }
    }

    private function updateCli(): void
    {
        $message = "WP CLI";
        if (!$this->dryRun) {
            \WP_CLI::runcommand('cli update --stable');
            $this->commit($message);
        }
        $this->outputMessage($message);
    }

    private function updateCore(): void
    {
        global $wp_version;

        $coreUpdates = get_core_updates();
        if (isset($coreUpdates[0]->current)) {
            if ($wp_version !== $coreUpdates[0]->current) {
                $message = $this->prepareMessage('Wordpress Core', $wp_version, $coreUpdates[0]->current);
                if (!$this->dryRun) {
                    \WP_CLI::runcommand('core update');
                    $this->commit($message);
                }
                $this->outputMessage($message);
            }
        }
    }

    private function updatePlugins(): void
    {
        $updates = get_plugin_updates();
        foreach ($updates as $update) {
            if (!isset($update->update)) {
                continue;
            }
            if (in_array($update->update->slug, $this->exclude)) {
                $this->skipped[] = $update->Name;
                continue;
            }
            $message = $this->prepareMessage($update->Name, $update->Version, $update->update->new_version);
            if (!$this->dryRun) {
                \WP_CLI::runcommand('plugin update ' . $update->update->slug);
                $this->commit($message);
            }
            $this->outputMessage($message);
        }
    }

    private function outputMessage($message, $type = 'log')
    {
        if ($type === 'warning') {
            \WP_CLI::warning("— " . $message);
        } else {
            \WP_CLI::log("— " . $message);
        }
    }

    private function prepareMessage($name, $currentVersion, $newVersion): string
    {
        return $name . ' - ' . $currentVersion . ' -> ' . $newVersion;
    }

    private function commit($message): void
    {
        system('cd ' . ABSPATH . '; git add . && git commit -am "' . $message . '"');
    }
}

/**
 * Helper command to discover plugin slugs to use in the exclude flag
 *
 * ## EXAMPLES
 * // php wp-cli.phar ep-list-plugin-slugs
 */
class ListPluginSlugs
{
    public function __invoke($args, $assoc_args)
    {
        $this->run();
    }

    public function run()
    {
        $updates = get_plugin_updates();
        foreach ($updates as $update) {
            if (!isset($update->update)) {
                continue;
            }
            \WP_CLI::log($update->update->slug);
        }
    }
}

add_action('cli_init', function () {
    \WP_CLI::add_command('ep-wp-update', 'Pulp\Update');
    \WP_CLI::add_command('ep-list-plugin-slugs', 'Pulp\ListPluginSlugs');
});
