<?php
declare(strict_types=1);

namespace Popin\Widget\Model\Csp;

use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Csp\Model\Policy\FetchPolicy;

/**
 * Adds CSP policies required by the Popin widget.
 *
 * The widget.js script and zoid framework dynamically create <style> elements
 * and inline style attributes on the parent page for widget positioning/layout.
 * Without 'unsafe-inline' in style-src, the widget container is unstyled and invisible.
 */
class CspPolicyCollector implements PolicyCollectorInterface
{
    /**
     * @inheritDoc
     */
    public function collect(array $defaultPolicies = []): array
    {
        $defaultPolicies[] = new FetchPolicy(
            'style-src',
            false,  // noneAllowed
            [],     // hostSources
            [],     // schemeSources
            false,  // selfAllowed
            true    // inlineAllowed — adds 'unsafe-inline'
        );

        return $defaultPolicies;
    }
}
