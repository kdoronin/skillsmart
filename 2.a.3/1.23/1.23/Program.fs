// 23.4.1
let (.+.) x y =
    let mutable (goldx, silverx, copperx) = x
    let mutable (goldy, silvery, coppery) = y
    let mutable (goldz, silverz, copperz) = (goldx + goldy, silverx + silvery + ((copperx + coppery) - (copperx + coppery)%12)/12, (copperx + coppery)%12)
    if silverz > 20 then
        goldz <- goldz + (silverz - silverz%20) / 20
        silverz <- silverz%20
        (goldz, silverz, copperz)
    else
        (goldz, silverz, copperz)

let (.-.) x y =
    let mutable (goldx, silverx, copperx) = x
    let mutable (goldy, silvery, coppery) = y
    if coppery > copperx then
        if silverx > 0 then
            silverx <- silverx - 1
            copperx <- copperx + 12 - coppery
        else
            if goldx > 0 then
                goldx <- goldx - 1
                silverx <- silverx + 20 - 1
                copperx <- copperx + 12 - coppery
            else
                copperx <- copperx - coppery
    else
        copperx <- copperx - coppery
    if silvery > silverx then
        if goldx > 0 then
            goldx <- goldx - 1
            silverx <- silverx + 20 - silvery
        else
            silverx <- silverx - silvery
    else
        silverx <- silverx - silvery
    goldx <- goldx - goldy
    (goldx, silverx, copperx)


// 23.4.2
//let (.+) x y = ...
//let (.-) x y = ...
//let (.*) x y = ...
//let (./) x y = ...

let c = (1, 38, 10) .+. (2, 1, 1)
let (goldres, silverres, copperres) = c
let (goldmin, silvermin, coppermin) = (3, 19, 10) .-. (2, 2, 11)

printf "Gold: %d \n" goldres
printf "Silver: %d \n" silverres
printf "Copper: %d \n" copperres
printf "\n _______ \n\n"
printfn "Gold: %d" goldmin
printfn "Silver: %d" silvermin
printfn "Copper: %d" coppermin