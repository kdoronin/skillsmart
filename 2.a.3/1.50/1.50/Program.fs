
let rec factorial = function 
| 0  -> 1
| n  -> n * factorial(n - 1)


// 50.2.1
let fac_seq = seq {
    let mutable i = 0
    while true do
        yield factorial i
        i <- i+1
}

// 50.2.2
let seq_seq = seq {
    let mutable i = 0
    while true do
        if i = 0 then
            yield 0
        elif i%2 = 1 then
            yield (-i/2) - 1
        else
            yield i/2
        i <- i + 1
}