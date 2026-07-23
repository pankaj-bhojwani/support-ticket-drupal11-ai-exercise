# Drupal 11 Development Rules

You are an expert Drupal 11 developer.

## General

- Follow Drupal Coding Standards (PSR-12).
- Prefer Drupal 11 APIs and avoid deprecated APIs.
- Generate one feature at a time.
- Explain the approach before generating code.
- Keep code modular and maintainable.
- Follow SOLID principles.
- Add PHPDoc to all classes and methods.
- Suggest appropriate Kernel or Functional tests after implementation.

## Architecture

- Prefer Content Entities over Content Types when custom business logic is required.
- Separate responsibilities between Entities, Forms, Services, Access Handlers, and Validation.
- Keep business logic inside services.
- Keep validation separate from business logic.
- Use ListBuilder for entity listings.

## Dependency Injection

- Prefer Dependency Injection over `\Drupal::service()`.
- Use `ContainerInjectionInterface` where appropriate.
- Register custom services in `*.services.yml`.

## Drupal Best Practices

- Use Entity API whenever possible.
- Use `ContentEntityForm` for entity forms.
- Use `EntityListBuilder` for entity listings.
- Use `AccessControlHandler` for entity permissions.
- Use custom validation constraints when business rules require them.
- Use configuration and routing YAML files following Drupal conventions.
- Use attributes instead of annotations where supported by Drupal 11.

## Code Generation

Always:

1. Explain the implementation.
2. Generate only the requested component.
3. Do not generate unrelated code.
4. Do not modify existing files unless requested.
5. Use Dependency Injection.
6. Avoid duplicate logic.
7. Keep classes focused on a single responsibility.

## Testing

For each feature, recommend:

- Kernel Tests
- Functional Tests

where applicable.
