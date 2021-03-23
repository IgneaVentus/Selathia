$(document).ready( function () {
    $("#mapSidePanel").children("button").click(function () {
        $("#maplock").show();
        if ($("#coordX").text()!="") {
            switch ($(this).attr("id")) {
                case "locCreate": modalCreate();
                                break;
                case "locRemove": modalRemove();
                                break;
                case "locModify": modalEdit();
                                break;
                default: return("Error, wrong modal type");
            }
        }
        else {
            $("#modal").css({width: "30vw"});
            $("#modal").children(".top").children(".header").text(
                "Błąd!"
            );
            $("#modal").children(".mid").text(
                "Nie wybrano żadnych koordynatów!"
            );
            $("#modal").show();
        }
    });
})

function modalCreate() {
    $("#modal").css({width: "60vw"});
    $("#modal").children(".top").children(".header").text(
        "Kreacja lokacji"
    );
    var loc = [($("#coordX").text()), ($("#coordY").text())];
    console.log(loc[0]+" "+loc[1])
    $("#modal").children(".mid").html(
        "<form name='newloc' method='post'><div class='row'><div class='label'>Nazwa lokacji:</div><input type='text' id='name'></div><div class='row'><div class='label'>Mapa lokacji:</div><input type='url' id='locmap'></div><div class='row'><div class='label'>Rodzaj lokacji:</div><input type=text name='kind'></div><div class='row'><div class='label'>Władca lokacji:</div><input type=text name='ruler'></div><div class='row'><div class='label'>Opis lokacji:</div><textarea rows='6' form='newlocl'></textarea></div><div class='row data'><input id='posX' name='posX' type='text' disabled><input id='posY' name='posY' type='text' disabled></div><div class='row'><input type='submit' value='Zapisz'></div>"
    );
    $("#posX").val(loc[0]);
    $("#posY").val(loc[1]);
    $("#modal").show();
}

function modalRemove() {
    $("#modal").css({width: "30vw"});
    $("#modal").children(".top").children(".header").text(
        "Usuwanie lokacji"
    );
    $("#modal").children(".mid").html(
        "Czy na pewno chcesz usunąć lokację? <div class='small-text'>Procesu nie da się cofnąć!</div>"
    );
    $("#modal").children(".bot").html(
        "<input type='submit' value='Wyślij'>"
    );
    $("#modal").show();
}

function modalEdit() {
    $("#modal").css({width: "60vw"});
    $("#modal").children(".top").children(".header").text(
        "Edycja lokacji"
    );
    $("#modal").children(".mid").html(
        "<form name='editloc' method='post'><div class='row'><div class='label'>Nazwa lokacji:</div><input type='text' id='name'></div><div class='row'><div class='label'>Mapa lokacji:</div><input type='url' id='locmap'></div><div class='row'><div class='label'>Rodzaj lokacji:</div><input type=text name='kind'></div><div class='row'><div class='label'>Władca lokacji:</div><input type=text name='ruler'></div><div class='row'><div class='label'>Opis lokacji:</div><textarea rows='6' form='newlocl'></textarea></div><div class='row data'><input id='posX' name='posX' type='text' disabled><input id='posY' name='posY' type='text' disabled></div><div class='row'><input type='submit' value='Zapisz'></div>"
    );
    $("#modal").show();
}