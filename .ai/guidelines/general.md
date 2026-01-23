## General code instructions
 
- Don't generate code comments above the methods or code blocks if they are obvious. Don't add docblock comments when defining variables, unless instructed to, like `/** @var \App\Models\User $currentUser */`. Generate comments only for something that needs extra explanation for the reasons why that code was written.
- For new features, you MUST generate Pest automated tests.
- For library documentation, if some library is not available in Laravel Boost 'search-docs', always use context7. Automatically use the Context7 MCP tools to resolve library id and get library docs without me having to explicitly ask.
- App is run in docker environment. When giving terminal commands, always prefix them with `docker-compose exec php ` unless instructed differently.
- When building frontend assets, always use `npm` commands. Use node container labeled as `node` in the docker-compose.yml file. Always prefix commands with `docker-compose exec node `.
- Check tasks/milestones in plan.md. Mark items as complete when done.
- In database migrations, ommit extra indexes on pk and fk columns, as Laravel adds them automatically.
---