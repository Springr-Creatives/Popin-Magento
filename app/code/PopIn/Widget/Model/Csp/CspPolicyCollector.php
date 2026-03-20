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
        // style-src: 'self' for the store's own CSS, 'unsafe-inline' for dynamically
        // created <style> elements by widget.js/zoid, and the host for widget.css
        $defaultPolicies[] = new FetchPolicy(
            'style-src',
            false,
            ['https://widget01.popin.to', 'https://*.popin.to'],
            [],
            true,   // selfAllowed — preserves 'self' for the store's own stylesheets
            true    // inlineAllowed — adds 'unsafe-inline' for dynamic <style> elements
        );

        // style-src-elem: explicit fallback so browsers supporting CSP Level 3
        // don't rely on style-src alone for <link> and <style> elements
        $defaultPolicies[] = new FetchPolicy(
            'style-src-elem',
            false,
            ['https://widget01.popin.to', 'https://*.popin.to'],
            [],
            true,
            true
        );

        return $defaultPolicies;
    }
}
