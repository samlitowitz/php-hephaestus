1. Input -> IR -> Output

# IR
## Scalars
1. Integer
1. String
1. Date
1. Time
1. DateTime
1. Currency
1. Binary

## Non-Scalars
1. TBD!

# Inputs
1. MySQL
   1. Table definition(s)
2. PgSQL
   1. Table definition(s)
3. Config file???

# Outputs
# Models
1. Plain old objects w/proper type hinting on getters, setters, and deserialize from array
   1. Validate on set (used by deserialize)

# Data Layer
1. Repository interface(s)
### Active Record
1. MySQL
2. PgSQL

### "Object Mapper"
1. MySQL
2. PgSQL

# CLI
1. How to configure?
   1. available sources/sinks?
   2. credential security
   3. host/port/etc flexibility
2. options/flags for transpiling
