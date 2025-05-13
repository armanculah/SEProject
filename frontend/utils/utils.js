const Utils = {
    init_spapp: function () {
        var app = $.spapp({
            defaultView: "#dashboard", //bilo koji main view
            templateDir: "frontend/views/"

        });
        app.run();
    }
}