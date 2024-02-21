let setZeros n =
    let mutable zero = ""
    for i=1 to n do
        zero <- zero + "0"
    zero

let rec intToBinary (i,n) =
    match i with
    | 0 | 1 -> setZeros n + string i
    | _ ->
        let bit = string (i % 2)
        (intToBinary ((i / 2), n-1)) + bit

let howMuchTrue s =
    let mutable result = 0
    for i in s do
        if i = '1' then
            result <- result + 1
    result

let generateSet s =
    let mutable result = Set.empty
    let mutable x = 1
    for i in s do
        if i = '1' then
            result <- Set.add x (result)
        x <- x + 1
    result


// 42.3
let allSubsets n k =
   let mutable result = Set.empty
   for i=0 to (int)(2.0**(float)n) do
        let s = intToBinary (i, n-1)
        if howMuchTrue s = k then
            result <- Set.add (generateSet s) (result)
   result