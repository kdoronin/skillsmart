// 40.1
let sum (p, xs) =
    let rec sumloop = function
    | (p, [], newli) -> newli
    | (p, head::tail, newli) when p head = true -> sumloop(p, tail, newli+head)
    | (p, head::tail, newli) when p head = false -> sumloop(p, tail, newli)
    | (p, xs, newli) -> sumloop(p, xs, newli)
    sumloop(p, xs, 0)

// 40.2.1
let count (xs, n) = 
    let rec countloop = function
    | ([], n, inlist) -> inlist
    | (head::tail, n, inlist) when head < n -> countloop(tail, n, inlist)
    | (head::tail, n, inlist) when head = n -> countloop(tail, n, inlist + 1)
    | (xs, n, inlist) -> inlist
    countloop(xs, n, 0)

// 40.2.2
let insert (xs, n) =
    let rec insertloop = function
    | ([], n, inserthead) -> inserthead @ [n]
    | (head::tail, n, inserthead) when head >= n -> inserthead @ n::head::tail
    | (head::head2::tail, n, inserthead) when head < n && head2 >= n -> inserthead @ head::n::head2::tail
    | (head::tail, n, inserthead) -> insertloop(tail, n, inserthead @ [head])
    insertloop(xs, n, [])

// 40.2.3
let intersect (xs1, xs2) =
    let rec sectloop = function
    | ([], xs2, res) -> res
    | (xs1, [], res) -> res
    | (head1::tail1, head2::tail2, res) when head1 < head2 -> sectloop(tail1, head2::tail2, res)
    | (head1::tail1, head2::tail2, res) when head1 > head2 -> sectloop(head1::tail1, tail2, res)
    | (head1::tail1, head2::tail2, res) when head1 = head2 -> sectloop(tail1, tail2, res @ [head1])
    | (xs1, xs2, res) -> res
    sectloop(xs1, xs2, [])

// 40.2.4
let plus (xs1, xs2) =
    let rec plusloop = function
    | ([], xs2, res) -> res @ xs2
    | (xs1, [], res) -> res @ xs1
    | (head1::tail1, head2::tail2, res) when head1 <= head2 -> plusloop(tail1, head2::tail2, res @ [head1])
    | (head1::tail1, head2::tail2, res) when head2 < head1 -> plusloop(head1::tail1, tail2, res @ [head2])
    | (xs1, xs2, res) -> res
    plusloop(xs1, xs2, [])

// 40.2.5
let minus (xs1, xs2) = 
    let rec minusloop = function
    | ([], [], res) -> res
    | (xs1, [], res) -> res @ xs1
    | ([], xs2, res) -> res
    | (head1::tail1, head2::tail2, res) when head1 = head2 -> minusloop(tail1, tail2, res)
    | (head1::tail1, head2::tail2, res) when head1 > head2 -> minusloop(head1::tail1, tail2, res)
    | (head1::tail1, head2::tail2, res) when head1 < head2 -> minusloop(tail1, head2::tail2, res @ [head1])
    minusloop(xs1, xs2, [])

// 40.3.1
let smallest xs1 =
    let rec smallestloop = function
    | ([], min) -> Some(min)
    | (head::tail, min) when head < min -> smallestloop(tail, head)
    | (head::tail, min) -> smallestloop(tail, min)
    let headmain::tailmain = xs1
    smallestloop(tailmain, headmain)

// 40.3.2
let delete (n, xs) =
    let rec deleteloop = function
    | (n, [], reshead) -> reshead
    | (n, head::tail, reshead) when n = head -> reshead @ tail
    | (n, head::tail, reshead) -> deleteloop(n, tail, reshead @ [head])
    deleteloop(n, xs, [])

// 40.3.3
let sort xs = 
    let rec sortloop = function
    | ([], res) -> res
    | (xs , res) -> sortloop(delete(smallest(xs).Value, xs), res @ [smallest(xs).Value])
    sortloop(xs, [])

let rev xs =
    let rec revloop = function
    | ([], res) -> res
    | (head::tail, res) -> revloop(tail, head::res)
    revloop(xs, [])

// 40.4
let revrev xs =
    let rec revrevloop = function
    | ([], res) -> res
    | (head::tail, res) -> revrevloop(tail, rev(head)::res)
    revrevloop(xs, [])