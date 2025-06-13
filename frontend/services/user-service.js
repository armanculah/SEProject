var UserService = {
    init: function () {
        var token = localStorage.getItem("user_token");
        if (token && token !== undefined) {
            window.location.replace("index.html");
        }
        $("#login-form").validate({
            submitHandler: function (form) {
                var entity = Object.fromEntries(new FormData(form).entries());
                UserService.login(entity);
            },
        });
    },
    login: function (entity) {
        RestClient.authenticate("auth/login", entity, function (result) {
            // Determine role from backend response
            let role = 'user';
            if ((result.role_id === 1 || result.role === 'admin' || result.role === 'Admin')) {
                role = 'admin';
            }
            localStorage.setItem("user_token", result.token);
            localStorage.setItem("role", role);
            if (role === 'admin') {
                window.location.hash = "#adminpanel";
            } else {
                window.location.hash = "#profile";
            }
        }, function (error) {
            toastr.error(error?.responseText ? error.responseText : 'Error');
        });
    },
    logout: function () {
        localStorage.clear();
        window.location.replace("login.html");
    },
    generateMenuItems: function(){
        const token = localStorage.getItem("user_token");
        const user = Utils.parseJwt(token).user;
        if (user && user.role){
            let nav = "";
            let main = "";
            switch(user.role) {
                case Constants.USER_ROLE:
                    nav = '<li class="nav-item mx-0 mx-lg-1">'+
                          '<a class="nav-link py-3 px-0 px-lg-3 rounded " href="#students">Students</a>'+ 
                          '</li>';
                    $("#tabs").html(nav);
                    main = '<section id="students" data-load="students.html"></section>';
                    $("#spapp").html(main);
                    break;
                case Constants.ADMIN_ROLE:
                    nav = '<li class="nav-item mx-0 mx-lg-1">'+
                          '<a class="nav-link py-3 px-0 px-lg-3 rounded " href="#students">Students</a>'+ 
                          '</li>';
                    $("#tabs").html(nav);
                    main = '<section id="students" data-load="students.html"></section>';
                    $("#spapp").html(main);
                    break;
                default:
                    $("#tabs").html(nav);
                    $("#spapp").html(main);
            }
        } else {
            window.location.replace("login.html");
        }
    },
    register: function (entity) {
        // Map frontend fields to backend expected fields
        const payload = {
            user_name: entity["register-username"],
            email: entity["register-email"],
            password: entity["register-password"]
        };
        RestClient.authenticate("auth/register", payload, function (result) {
            let role = 'user';
            if (result.data && (result.data.role_id === 1 || result.data.role === 'admin' || result.data.role === 'Admin')) {
                role = 'admin';
            }
            localStorage.setItem("user_token", result.data.token);
            localStorage.setItem("role", role);
            if (role === 'admin') {
                window.location.replace("adminpanel.html");
            } else {
                window.location.replace("profile.html");
            }
        }, function (error) {
            toastr.error(error?.responseText ? error.responseText : 'Error');
        });
    }
};