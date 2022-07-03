type TimeOfDay = { hours: int; minutes: int; f: string }

let (.>.) x y =
    let mutable retval = false
    if x.f = "PM" && y.f = "AM" then
        retval <- true
    elif x.f = "AM" && y.f = "PM" then
        retval <- false
    elif x.hours > y.hours then
        retval <- true
    elif x.hours < y.hours then
        retval <- false
    elif x.minutes > y.minutes then
        retval <- true
    else retval <- false
    retval