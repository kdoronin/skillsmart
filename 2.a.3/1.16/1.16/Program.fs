// 16.1
let notDivisible  = function
| (n,m) -> m%n = 0

// 16.2
let prime n =
    let mutable result = true
    for i in 1..n-1 do
        if n%i = 0 && i > 1 then
            result <- false
    result