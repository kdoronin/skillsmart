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
                copperx <- 0
                silverx <- 0
                goldx <- 0
    else
        copperx <- copperx - coppery
    if silvery > silverx then
        if goldx > 0 then
            goldx <- goldx - 1
            silverx <- silverx + 20 - silvery
        else
            copperx <- 0
            silverx <- 0
            goldx <- 0
    else
        silverx <- silverx - silvery
    if goldy > goldx then
        goldx <- 0
        silverx <- 0
        copperx <- 0
    else
        goldx <- goldx - goldy
    (goldx, silverx, copperx)


// 23.4.2
let (.+) x y =
    let mutable (a, b) = x
    let mutable (c, d) = y
    (a+c, b+d)
let (.-) x y =
    let mutable (a, b) = x
    let mutable (c, d) = y
    (a - c, b - d)
let (.*) x y =
    let mutable (a, b) = x
    let mutable (c, d) = y
    (a*c - b*d, b*c + a*d)
let (./) x y =
    let mutable (a:float, b:float) = x
    let mutable (c:float, d:float) = y
    ((a*c + b*d) / (c*c + d*d), (b*c - a*d) / (c*c + d*d))