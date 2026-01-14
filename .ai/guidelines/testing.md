## Testing instructions
 
### Before Writing Tests
 
  1. **Check database schema** - Use `database-schema` tool to understand:
     - Which columns have defaults
     - Which columns are nullable
     - Foreign key relationship names
 
  2. **Verify relationship names** - Read the model file to confirm:
     - Exact relationship method names (not assumed from column names)
     - Return types and related models
 
  3. **Test realistic states** - Don't assume:
     - Empty model = all nulls (check for defaults)
     - `user_id` foreign key = `user()` relationship (could be `author()`, `employer()`, etc.)
     - When testing form submissions that redirect back with errors, assert that old input is preserved using `assertSessionHasOldInput()`.
 
---