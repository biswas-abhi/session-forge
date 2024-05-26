[![License](https://img.shields.io/badge/License-Apache_2.0-282661.svg)](https://opensource.org/licenses/Apache-2.0)
[![PHP ^8.0](https://img.shields.io/badge/php-%5E8.0-7a86b8)](https://www.php.net/releases/8.0/en.php)
[![PHP json](https://img.shields.io/badge/php-json-181818)](https://www.php.net/manual/en/book.json.php)
[![PHP Libsodium](https://img.shields.io/badge/php-sodium-aaaaff)](https://www.php.net/manual/en/book.sodium.php)
[![PHP Zlib](https://img.shields.io/badge/php-zlib-2fbf2f)](https://www.php.net/manual/en/book.zlib.php)
[![Status Beta](https://img.shields.io/badge/status-beta-orange)]()
[![Tag v1.0.0-dev](https://img.shields.io/badge/tag-v1.0.0--dev-orange)]()

# SessionForge

`SessionForge` is an open-source _PHP_ library designed to provide developers with a robust and flexible solution for managing sessions using _file manipulation_. With `SessionForge`, you can effortlessly create, read, update, and delete session data stored in files, making it ideal for applications that require lightweight session management without the complexity of database dependencies.

## Features

1. **Create Sessions:** Easily initialize new session files with unique identifiers.
2. **Read Sessions:** Retrieve session data from files with simple and intuitive commands.
3. **Update Sessions:** Modify existing session data and ensure changes are accurately saved.
4. **Delete Sessions:** Remove session files when they are no longer needed, keeping your storage clean and efficient.

## Technology Stack

1. **PHP Compatibility**: Fully compatible with the most recent four versions of the latest PHP release (_PHP 8.0_).
2. **Extensions Utilized**:
   1. **Libsodium:** Ensures secure encryption and decryption of session data.
   2. **JSON:** Enables efficient handling and storage of session data in JSON format.
   3. **Zlib:** Efficient Compression for Optimized Data Storage

## Installation

```sh
composer require biswas-abhishek/session-forge
```

## Development Installation

1. Clone the repository from `GitHub`.
2. Navigate to the project directory.
3. Install dependencies using `Composer`.

## Usage

1. **Creating a Session:** Initialize a new session file with a unique identifier.
2. **Reading a Session:** Retrieve session data from a file using the session ID.
3. **Updating a Session:** Modify existing session data and save the changes.
4. **Deleting a Session:** Remove session files that are no longer needed.

## Security

`SessionForge` uses the libsodium extension to encrypt session data. Ensure that libsodium is installed and enabled in your PHP environment.

## Contributing

We welcome contributions! Please read our Contributing Guidelines for more information.

## License

`SessionForge` is released under the `Apache License 2.0`. See the LICENSE file for more details.

## Future Goals for SessionForge

1. **Add XML and YAML Support:** Enhance the flexibility of `SessionForge` by introducing support for XML and YAML file formats.

2. **Implement Public Key Encryption:** Increase the security of session data by incorporating public key encryption mechanisms.

3. **Expand Hashing Options:** Offer a broader selection of hashing algorithms to meet diverse security needs. By supporting more hashing options, developers can choose the most suitable and secure hashing methods for their specific use cases.

4. **Provide More Private Encryption Methods:** Allow users to select from a variety of private encryption methods to better secure session data.

---

#### Visit [Documentation](https://sessionforge.netlify.app/)
