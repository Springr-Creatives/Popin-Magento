# Popin Video Call Widget for Magento 2

Integrate [Popin](https://popin.to) live video shopping into your Magento 2 store. This module adds the Popin video call widget to your storefront, enabling real-time video conversations between customers and sales representatives.

When a logged-in customer initiates a video call, their profile information (name, email, phone, postal code) is automatically passed to the Popin widget so your agents have context before the conversation begins.

## Requirements

- Magento 2.x
- PHP >= 7.4
- A Popin account and widget token (sign up at [popin.to](https://popin.to))

## Installation

### Option A: Manual Installation (recommended for this module)

1. **Download the module** from this repository:

   ```bash
   git clone https://github.com/Springr-Creatives/Popin-Magento.git
   ```

2. **Copy the module** into your Magento installation:

   ```bash
   cp -r Popin-Magento/app/code/PopIn <your-magento-root>/app/code/PopIn
   ```

3. **Enable the module and run setup:**

   ```bash
   cd <your-magento-root>
   bin/magento module:enable PopIn_Widget
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
   bin/magento module:enable PopIn_Widget
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
   | **Popin Token**         | Enter the token from your [Popin dashboard](https://popin.to). Required for the widget to function. |
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

## Uninstallation

### If installed manually:

```bash
bin/magento module:disable PopIn_Widget
bin/magento setup:upgrade
rm -rf app/code/PopIn
bin/magento cache:flush
```

### If installed via Composer:

```bash
bin/magento module:disable PopIn_Widget
composer remove nowflow/module-popin-widget
bin/magento setup:upgrade
bin/magento cache:flush
```

## Support

For questions about the Popin platform or to get your widget token, visit [popin.to](https://popin.to).

## License

Proprietary - Nowflow Technologies Pvt Ltd
