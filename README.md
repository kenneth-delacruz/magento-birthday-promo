# Magento Birthday Promo - Installation Guide

## Introduction

This guide provides step-by-step instructions to set up and run the **Magento Birthday Promo** module. The module applies a default birthday discount to customers and adds a birthday promo condition in Magento 2 cart price rules. The setup process is designed to work with **Mark Shust's Docker Magento** environment.

## Prerequisites

Ensure you have the following before proceeding:

- Docker installed on your machine
- Magento 2.4.7-p3 (Community Edition) compatibility
- Mark Shust's Docker Magento setup
- Git installed on your system

## Installation Steps

### 1. Clone the Repository

Navigate to your development directory and clone the repository:
```sh
cd $/Sites
git clone https://github.com/kenneth-delacruz/magento-birthday-promo.git
```

### 2. Create and Navigate to the Project Directory

Create a directory for testing the module and move into it:
```sh
mkdir -p ~/Sites/magento-birthday-promo-test
cd $_
```

### 3. Set Up the Magento Environment

Run the following command to set up Magento using Mark Shust's Docker Magento:
```sh
curl -s https://raw.githubusercontent.com/markshust/docker-magento/master/lib/onelinesetup | bash -s -- magento.test community 2.4.7-p3
```
**Note:** This will set up a Magento instance with the domain `magento.test`.

### 4. Replace Default `app/code` with Module Source Code

Replace the default `app/code` directory with the module's source code:
```sh
cp -R ~/Sites/magento-birthday-promo/src/app/code src/app/
```

### 5. Start the Magento Environment

Run the following command to start the Docker container:
```sh
bin/start
```

### 6. Deploy Sample Data (Optional)

To add Magento's sample data, execute:
```sh
bin/magento sampledata:deploy
```

### 7. Enable and Configure the Module

Enable the **Magento Birthday Promo** module and disable unnecessary authentication modules:
```sh
bin/magento module:enable Kdc_BirthdayPromo
bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth
```

### 8. Upgrade Magento Setup

Run the following command to apply the necessary database changes:
```sh
bin/magento setup:upgrade
```

### 9. Access Your Magento Store

Once setup is complete, open your browser and go to:
```
http://magento.test
```

## Notes

- Ensure that Docker is running before executing `bin/start`.

---

Enjoy your Magento Birthday Promo setup! 🎉

