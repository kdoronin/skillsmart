// 39.1
let rec rmodd = function
| [] -> []
| [x] -> []
| head :: (head2 :: tail) -> head2 :: rmodd tail



// 39.2
let rec del_even = function
| [] -> []
| head :: tail when head%2 = 0 -> del_even tail
| head :: tail -> head :: del_even tail

// 39.3
let multiplicity x xs =
    let rec loop = function
        | (x,[],z) -> z
        | (x, head::tail, z) when head = x ->  loop(x, tail, z+1)
        | (x, head::tail, z) when head <> x -> loop(x, tail, z)
        | (x, xs, z) -> z
    loop (x, xs, 0)

// 39.4
let split n =
    let rec splitloop = function
        | ([], a, b, num) -> (a, b)
        | (head::tail, a, b, num) when num%2 = 0 || num = 0 -> splitloop(tail, a @ [head], b, num+1)
        | (head::tail, a, b, num) when num%2 <> 0 -> splitloop(tail, a, b @ [head] , num+1)
        | (n, a, b, num) -> (a, b)
    splitloop (n, [], [], 0)


// 39.5
let zip (xs1,xs2) =
    let rec ziploop = function
        | ([], [], res) -> res
        | (xs1, xs2, []) when xs1.Length <> xs2.Length -> failwith "Длины входных списков неодинаковы"
        | (head::tail, head2::tail2, res) -> ziploop (tail, tail2, res @ [(head, head2)])
        | (xs1, xs2, res) -> res
    ziploop (xs1, xs2, [])