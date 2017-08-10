# Release Notes

# v1.5.4 (2017-08-10)

### Fixed
- Fixed interactive element examples
- Fixed removed by accident \JsonSerializable from list of implemented Action's interfaces

## v1.5.2 (2017-08-09)

### Added
- Added separate classes for button answer and menu answer
- Added response type to delayed message, so it can use in_channel like regular response
- Added response url for interactive message, so we can delay response to an answer
- Added getValue, getSelectedOptions and getSelectedOptionValue to InteractiveRequest to easier retrieve answer value


### Changed
- Changed answer's value to be optional

### Fixed
- Fixed immediate response to be properly flushed when php uses fastcgi
- Fixed exception with missing buffer to delete by checking if there is any content to erase first

## v1.0.0 (2017-08-04)

### Added
- Initial commit with integration handling slash commands, message builder, webhooks, interactive messages and delayed messages
