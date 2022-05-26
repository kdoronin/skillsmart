let days_in_month = function
|1|3|5|7|8|10|12 -> 31
|2 -> 28
|n when n < 12 && n > 0 -> 30
| _ -> 0
