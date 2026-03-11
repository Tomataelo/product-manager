# product-manager

Hi, here is a brief summary of the architectural decisions made during development:

- Used `#[MapRequestPayload]` instead of serializer for cleaner request handling
- DTOs with Symfony Validator constraints for input validation and data transfer
- Symfony EventDispatcher for domain event handling (ProductPriceChanged)
- Doctrine Paginator for product listing pagination
- Optimistic Locking for concurrency handling

That's about it, I think 😄
