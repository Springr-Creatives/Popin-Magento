# Popin Video Call Widget for Magento 2

Integrate [Popin](https://popin.to) live video shopping into your Magento 2 store. This module adds the Popin video call widget to your storefront, enabling real-time video conversations between customers and sales representatives.

When a logged-in customer initiates a video call, their profile information (name, email, phone, postal code) is automatically passed to the Popin widget so your agents have context before the conversation begins.

## Requirements

- Magento 2.x
- PHP >= 7.4
- A Popin account and widget token (get it from your dashboard at [dashboard.popin.to](https://dashboard.popin.to))

## Installation

### Option A: Manual Installation (recommended for this module)

1. **Download the module** from this repository:

   ```bash
   git clone https://github.com/Springr-Creatives/Popin-Magento.git
   ```

2. **Copy the module** into your Magento installation:

   ```bash
   cp -r Popin-Magento/app/code/Popin <your-magento-root>/app/code/Popin
   ```

3. **Enable the module and run setup:**

   ```bash
   cd <your-magento-root>
   bin/magento module:enable Popin_Widget
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:flush
   ```

### Option B: Composer Installation

1. **Add the repository** to your Magento project's `composer.json`:

   ```bash
   cd <your-magento-root>
   composer config repositories.popin-widget vcs https://github.com/Springr-Creatives/Popin-Magento.git
   ```

2. **Require the package:**

   ```bash
   composer require nowflow/module-popin-widget
   ```

3. **Enable the module and run setup:**

   ```bash
   bin/magento module:enable Popin_Widget
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:flush
   ```

## Configuration

1. In the Magento Admin, navigate to **Stores > Configuration > Popin > Video Call Widget**.

2. Configure the following settings:

   | Setting              | Description                                                                                          |
   | -------------------- | ---------------------------------------------------------------------------------------------------- |
   | **Enable Popin Widget** | Set to **Yes** to activate the widget on your storefront.                                           |
   | **Popin Token**         | Enter the token from your [Popin dashboard](https://dashboard.popin.to). Required for the widget to function. |
   | **Widget Mode**         | **Hidden** — widget is triggered via a button click. **Visible** — widget launcher appears automatically on page load. |

3. Click **Save Config** and flush the cache:

   ```bash
   bin/magento cache:flush
   ```

All three settings can be configured per **Store View**, **Website**, or **Default** scope, allowing different configurations for different storefronts.

## How It Works

Once enabled, the module injects the Popin widget script on every frontend page (before `</body>`). The widget connects to Popin's servers using your token and initializes in the selected mode.

**Customer data capture:** For logged-in customers, the module automatically collects and passes the following information to the Popin widget:

- **Name** — from the customer profile
- **Email** — from the customer profile
- **Phone** — from the default billing address
- **Postal code** — from the default billing address

Guest visitors can still use the widget; they simply won't have pre-filled information.

## Adding a Custom Button (Hidden Mode)

When the widget mode is set to **Hidden**, the Popin launcher does not appear automatically. You need to add a button to your template that opens the widget on click.

Use the `Popin('open')` JavaScript call to trigger the widget. Place the button wherever you want it to appear — for example, on product detail pages, the header, or a CMS block.

**Basic example:**

```html
<button onclick="Popin('open')">Live Video Shopping</button>
```

**Styled example:**

```html
<button onclick="Popin('open')" class="action primary" style="background-color: #ff5722; border: none; color: #fff; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
    Start Video Call
</button>
```

### Where to Place the Button

In Magento 2, you can add the button in any of these ways:

1. **CMS Block / Page** — Go to **Content > Blocks** (or **Pages**) in the Admin and add the button HTML directly in the content editor (use the HTML/source mode).

2. **PHTML Template** — Add the button to a template file in your theme. For example, to add it on product pages, override the product view template in your theme:

   ```
   app/design/frontend/<Vendor>/<Theme>/Magento_Catalog/templates/product/view.phtml
   ```

3. **Layout XML** — Add a CMS block reference via layout XML to inject the button into a specific container on certain pages.

> **Note:** The button will only work on pages where the Popin widget script is loaded. Since this module injects the script on all frontend pages, the button can be placed anywhere on your storefront.

## Updating

### If installed manually:

1. **Download the latest version:**

   ```bash
   git clone https://github.com/Springr-Creatives/Popin-Magento.git
   ```

2. **Replace the module files** in your Magento installation:

   ```bash
   rm -rf <your-magento-root>/app/code/Popin/Widget
   cp -r Popin-Magento/app/code/Popin <your-magento-root>/app/code/Popin
   ```

3. **Run setup:**

   ```bash
   cd <your-magento-root>
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:flush
   ```

### If installed via Composer:

```bash
cd <your-magento-root>
composer update nowflow/module-popin-widget
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

> **Note:** If you previously had to whitelist `*.popin.to` in Magento's Content Security Policy manually, this update makes that unnecessary. The module now handles CSP configuration automatically. You can remove any manual CSP entries you added for Popin.

## Uninstallation

### If installed manually:

```bash
bin/magento module:disable Popin_Widget
bin/magento setup:upgrade
rm -rf app/code/Popin
bin/magento cache:flush
```

### If installed via Composer:

```bash
bin/magento module:disable Popin_Widget
composer remove nowflow/module-popin-widget
bin/magento setup:upgrade
bin/magento cache:flush
```

## Support

For questions about the Popin platform or to get your widget token, visit [popin.to](https://popin.to).

## License

Proprietary - Nowflow Technologies Pvt Ltd
