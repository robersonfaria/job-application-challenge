# Job Application Challenge
Attachment: JSON file: ["challenge.json"](storage/app/challenge.json).

## Task
Write a process that neatly writes the contents of the JSON file away to a database.
Preferably as a background task in Laravel. Use of Docker is allowed, but only if it makes it more fun.

We pay particular attention to the design and structure of the code. For example, we'd rather see a solid, neat, easily maintainable solution that doesn't work 100% of the time, than a finished solution that is messy and inimitable. We are particularly interested in the thinking behind your approach.

## Prerequisites:
- [Primary] Make the process such that any time it can be truncated (e.g., by a SIGTERM, power outage, etc.), it can continue in a robust, reliable manner exactly where it last left off (without duplicating data).1
- Design your solution "for growth," taking into account a hypothetical customer who will have new requirements each time.
- Use a solid, but not excessive database model. Code for Eloquent models and relationships are not important here, we are more concerned with the data structure.
- Only process records where the age is between 18 and 65 (or unknown).
  
## Bonus
  As an added challenge, we provide the following for consideration:
- What if the source file suddenly becomes 500 times larger?
- Is the process easily deployed for an XML or CSV file with similar content?
- Suppose that only records need to be processed for which the credit card number contains three consecutive same digits, how would you handle that?

1 Note that there is no guarantee that there are no duplicate records in the source file (there
  are no guaranteed unique (combinations of) properties) - and all duplicates do need to be
  replicated 1-to-1 in the database.
