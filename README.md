# phpIPAM API Client

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Composer](https://img.shields.io/badge/composer-package-orange.svg)](https://packagist.org/packages/sherinbloemendaal/phpipam-client)

A modern, fully-typed PHP client library for the [phpIPAM](https://phpipam.net/) API. This library provides an object-oriented interface for managing IP addresses, subnets, VLANs, and other network resources through phpIPAM's REST API.

## ✨ Features

- 🔒 **Secure connections** with SSL and encryption support
- 🏗️ **Object-oriented design** with full type safety (PHP 8.1+)
- 🚀 **Modern PHP practices** with strict types and union types
- 📚 **Comprehensive API coverage** for all phpIPAM resources
- 🛡️ **Exception handling** with detailed error responses
- 🔄 **Automatic object/ID conversion** for seamless development
- 📖 **Fluent interface** for method chaining

## 📋 Requirements

- **PHP 8.1** or higher
- **ext-json** extension
- **ext-openssl** extension (for encryption)
- **Guzzle HTTP** library
- **phpIPAM** server with API enabled

## 📦 Installation

Install via Composer:

```bash
# Latest stable release
composer require sherinbloemendaal/phpipam-client
```

## 🚀 Quick Start

### Basic Connection

```php
<?php

declare(strict_types=1);

use SherinBloemendaal\PhpIPAMClient\PhpIPAMClient;
use SherinBloemendaal\PhpIPAMClient\Connection\Connection;

// Initialize client with SSL (recommended)
$client = new PhpIPAMClient(
    url: 'https://phpipam.example.com',
    appID: 'myApp',
    username: 'admin',
    password: 'secret',
    apiKey: '', // Not needed for SSL
    securityMethod: Connection::SECURITY_METHOD_SSL
);

// Or initialize connection statically
Connection::initializeConnection(
    url: 'https://phpipam.example.com',
    appID: 'myApp', 
    username: 'admin',
    password: 'secret'
);
```

### Working with Sections

```php
use SherinBloemendaal\PhpIPAMClient\Controller\Section;

// Get all sections
$sections = Section::getAll();

// Get section by ID
$section = Section::getByID(1);

// Get section by name
$section = Section::getByName('Production');

// Create new section
$section = Section::post([
    'name' => 'Development',
    'description' => 'Development networks',
    'strictMode' => true
]);

// Update section
$section->setDescription('Updated description')
        ->setStrictMode(false)
        ->patch();

// Delete section
$section->delete();
```

### Working with Subnets

```php
use SherinBloemendaal\PhpIPAMClient\Controller\Subnet;
use SherinBloemendaal\PhpIPAMClient\Controller\Section;

// Get all subnets
$subnets = Subnet::getAll();

// Create subnet in a section
$section = Section::getByName('Production');
$subnet = Subnet::post([
    'subnet' => '192.168.1.0',
    'mask' => 24,
    'description' => 'Web servers',
    'sectionId' => $section, // Can use object or ID
]);

// Get subnet usage
$usage = $subnet->getUsage();

// Get addresses in subnet  
$addresses = $subnet->getAddresses();

// Find first free IP
$freeIP = $subnet->getFirstFree();
```

### Working with IP Addresses

```php
use SherinBloemendaal\PhpIPAMClient\Controller\Address;

// Search for IP address
$addresses = Address::getSearchByIP('192.168.1.100');

// Get address by IP and subnet
$address = Address::getByIPAndSubnet('192.168.1.100', $subnet);

// Create new address
$address = Address::postFirstFree($subnet, [
    'hostname' => 'web01.example.com',
    'description' => 'Web server',
    'owner' => 'IT Department'
]);

// Update address
$address->setHostname('web01-new.example.com')
        ->setDescription('Updated web server')
        ->patch();
```

## 🔐 Security Methods

The client supports three security methods:

### SSL (Recommended)
```php
$client = new PhpIPAMClient(
    url: 'https://phpipam.example.com',
    appID: 'myApp',
    username: 'admin', 
    password: 'secret',
    apiKey: '',
    securityMethod: Connection::SECURITY_METHOD_SSL
);
```

### Encryption
```php
$client = new PhpIPAMClient(
    url: 'http://phpipam.example.com',
    appID: 'myApp',
    username: '',
    password: '',
    apiKey: 'your-api-key',
    securityMethod: Connection::SECURITY_METHOD_CRYPT
);
```

### Both (Maximum Security)
```php
$client = new PhpIPAMClient(
    url: 'https://phpipam.example.com',
    appID: 'myApp',
    username: 'admin',
    password: 'secret', 
    apiKey: 'your-api-key',
    securityMethod: Connection::SECURITY_METHOD_BOTH
);
```

## 🎯 Usage Patterns

### Object-Oriented Approach (Recommended)

Use controller classes for type-safe, object-oriented API interaction:

```php
use SherinBloemendaal\PhpIPAMClient\Controller\{Section, Subnet, Address, VLAN};

// Fluent interface with method chaining
$section = Section::getByName('Production')
    ->setDescription('Production environment')
    ->patch();

// Object relationships - automatic ID conversion
$subnet = Subnet::post([
    'subnet' => '10.0.0.0',
    'mask' => 16,
    'sectionId' => $section, // Pass object, not ID
]);

// Work with related objects
$addresses = $subnet->getAddresses();
$firstFree = Address::postFirstFree($subnet, [
    'hostname' => 'server01.prod.local'
]);
```

### Direct API Calls

For advanced use cases, call the API directly:

```php
// Raw API call
$response = $client->call('GET', 'sections', [1], []);
$data = $response->getData();

// Check response
if ($response->isSuccess()) {
    echo "Success: " . $response->getMessage();
} else {
    echo "Error: " . $response->getMessage();
}
```

## 🗂️ Available Controllers

| Controller | Description | Key Methods |
|------------|-------------|-------------|
| **Section** | Network sections | `getAll()`, `getByName()`, `getAllSubnets()` |
| **Subnet** | IP subnets | `getUsage()`, `getAddresses()`, `getFirstFree()` |
| **Address** | IP addresses | `getSearchByIP()`, `postFirstFree()`, `getPing()` |
| **VLAN** | Virtual LANs | `getSubnets()`, `getSearch()` |
| **VRF** | Virtual routing | `getSubnets()`, `getSections()` |
| **Device** | Network devices | `getAddresses()`, `getSubnets()` |
| **L2Domain** | Layer 2 domains | `getVLANs()` |

## ⚠️ Exception Handling

The library provides two exception types:

```php
use SherinBloemendaal\PhpIPAMClient\Exception\{PhpIPAMException, PhpIPAMRequestException};

try {
    $section = Section::getByID(999);
} catch (PhpIPAMRequestException $e) {
    // API request failed
    $response = $e->getResponse();
    echo "API Error: " . $response->getMessage();
    echo "HTTP Code: " . $response->getCode();
} catch (PhpIPAMException $e) {
    // Client library error  
    echo "Client Error: " . $e->getMessage();
}
```

## 🔧 Advanced Configuration

### Custom Connection Management

```php
use SherinBloemendaal\PhpIPAMClient\Connection\Connection;

// Initialize once, use everywhere
Connection::initializeConnection(
    url: 'https://phpipam.example.com',
    appID: 'myApp',
    username: 'admin',
    password: 'secret'
);

// Get connection instance
$connection = Connection::getInstance();

// Check token status
$token = $connection->getToken();
$expires = $connection->getTokenExpires();
```

### Working with Custom Fields

```php
// Get custom fields for addresses
$customFields = Address::getCustomFields();

// Create address with custom fields
$address = Address::postFirstFree($subnet, [
    'hostname' => 'server01',
    'custom_Environment' => 'Production',
    'custom_Owner' => 'IT Team'
]);
```

## 📚 API Reference

### Core Methods

All controllers inherit these base methods:

- `getAll(): array` - Get all objects
- `getByID(int $id): static` - Get object by ID  
- `post(array $params): static` - Create new object
- `patch(array $params): bool` - Update object
- `delete(): bool` - Delete object

### Response Object

```php
$response = $client->call('GET', 'sections');

$response->getCode();      // HTTP status code
$response->isSuccess();    // Success boolean
$response->getMessage();   // Response message
$response->getData();      // Response data
$response->getTime();      // Response time
$response->getBody();      // Full response body
```

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. **Code Style**: Follow PSR-12 coding standards
2. **Type Safety**: Use strict types and proper type declarations
3. **Testing**: Add tests for new features
4. **Documentation**: Update documentation for API changes

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🔗 Related Links

- [phpIPAM Homepage](https://phpipam.net/)
- [phpIPAM API Documentation](https://phpipam.net/api/api_documentation/)
- [phpIPAM GitHub Repository](https://github.com/phpipam/phpipam)

## 🆘 Support

- **Issues**: [GitHub Issues](https://github.com/sherinbloemendaal/phpipam-client/issues)
- **Documentation**: [API Documentation](docs/)
- **Community**: [phpIPAM Community](https://phpipam.net/community/)

---

Made with ❤️ for the phpIPAM community
