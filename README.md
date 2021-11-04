# Clearfacts codestyle component

This component provides an integration with php codesniffer for your php projects.

## Installation

- Add the following to composer:

```json
    ...,
    "repositories": [
        ...,
        { "type": "vcs", "url": "https://github.com/Clearfacts/cf-codestyle" }
    ],
    ...
```

- `composer require clearfacts/codestyle`
  
## Usage

- Run the following to setup git hooks: `bin/cf-codestyle clearfacts:codestyle:hooks-setup`
- Optionally you can pass `--container`to setup which docker-container to use, and your root directory.
