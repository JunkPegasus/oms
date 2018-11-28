var userList;
var objectList;
var oTemplates;
var objectSubList;
var oEditableObject;

$(document).ready(function() {
    getUserList(1);

    // Dieser Request holt alle HTML Templates vom Server
    getTemplates();
});

function getTemplates() {
    $.post({
        url: "Services/TemplateService.php",
        data: {
            request: "allTemplates"
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                bTry = false;
                console.log(e);
            }
            if (bTry) {
                oTemplates = response;
            }
        }
    });
}


String.prototype.replaceAll = function(str1, str2, ignore) {
    return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, "\\$&"), (ignore ? "gi" : "g")), (typeof(str2) == "string") ? str2.replace(/\$/g, "$$$$") : str2);
}

function getUserList(mode) {
    $.post({
        url: "Services/UserService.php",
        data: {
            request: "userList"
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                bTry = false;
            }
            if (bTry) {
                if (response.success === true) {
                    userList = response.data;
                    if (mode == 2) {
                        showUserManagement();
                    }
                } else {
                    userList = null;
                }
            }
        }
    });
}

function getUserData(userID) {
    $.post({
        url: "Services/UserService.php",
        data: {
            request: "userDetails",
            id: userID
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    showUserEditWindow(response.data);
                }
            } else {}
        }

    });
}

function getObjectList() {
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "objectList"
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    objectList = response.data;
                    showObjectManagement();
                }
            } else {}
        }

    });
}

function getObjectSubList(id) {
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "objectSubList",
            listId: id
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    objectSubList = response.data;
                    showObjectSubManagement();
                }
            } else {}
        }

    });
}

function sendUserToServer(user) {
    showStatusBar("Speichern...");
    $.post({
        url: "Services/UserService.php",
        data: {
            request: "editOrAddUser",
            user: user
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    changeStatusBarStatus("success");
                    changeStatusBarText("Erfolgreich gespeichert");
                    getUserList(2);
                    closeUserEditWindow();
                } else {
                    changeStatusBarStatus("error");
                    changeStatusBarText("Fehler beim Speichern");
                }
            } else {
                changeStatusBarStatus("error");
                changeStatusBarText("Fehler beim Speichern");
            }
        }

    });
}

function deleteUserRequest(id) {
    showStatusBar("Löschen...");
    $.post({
        url: "Services/UserService.php",
        data: {
            request: "deleteUser",
            id: id
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    changeStatusBarStatus("success");
                    changeStatusBarText("Erfolgreich gelöscht");
                    getUserList(2);
                    closeUserEditWindow();
                } else {
                    changeStatusBarStatus("error");
                    changeStatusBarText("Fehler beim Löschen");
                }
            } else {
                changeStatusBarStatus("error");
                changeStatusBarText("Fehler beim Löschen");
            }
        }

    });
}

function deleteUserWithStatus(id, statusId) {
    $('#' + statusId).addClass("loading");
    $.post({
        url: "Services/UserService.php",
        data: {
            request: "deleteUser",
            id: id
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    $('#' + statusId).removeClass("loading").addClass("success");
                    getUserList(2);
                    closeUserEditWindow();
                } else {
                    $('#' + statusId).removeClass("loading").addClass("error");
                }
            } else {
                $('#' + statusId).removeClass("loading").addClass("error");
            }
        }

    });
}

function loadObjectData(id) {
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "object",
            objId: id
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    oEditableObject = response.data;
                    showEditObject();
                }
            } else {}
        }

    });
}



function deleteObjectRequest(id, refId) {
    showStatusBar("Löschen...");
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "deleteObject",
            objId: id
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    changeStatusBarStatus("success");
                    changeStatusBarText("Erfolgreich gelöscht");
                    getObjectSubList(refId);
                } else {
                    changeStatusBarStatus("error");
                    changeStatusBarText("Fehler beim Löschen");
                }
            } else {
                changeStatusBarStatus("error");
                changeStatusBarText("Fehler beim Löschen");
            }
        }

    });
}


function createObject(obj, id) {
    showStatusBar("Speichern...");
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "createObject",
            obj: obj
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    changeStatusBarStatus("success");
                    changeStatusBarText("Erfolgreich erstellt");
                    getObjectSubList(id);
                } else {
                    changeStatusBarStatus("error");
                    changeStatusBarText("Fehler beim Erstellen");
                }
            } else {
                changeStatusBarStatus("error");
                changeStatusBarText("Fehler beim Erstellen");
            }
        }

    });
}

function sendCreateObjectRequest(id) {
    var obj = { name: $('#objectName').val(), refId: id };
    createObject(obj, id);
    $('.createObjectWindow').remove();
    return false;
}

function changeObjectPublicStatus(id) {
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "changePublic",
            id: id
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    $('.objectEdit_Heading').toggleClass("public");
                }
            }
        }
    });
}

function saveFieldDataRequest(fields) {
    showStatusBar("Speichern...");
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "saveFieldData",
            fields: fields
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    changeStatusBarStatus("success");
                    changeStatusBarText("Erfolgreich gespeichert");
                    editObjectWithId(fields[0].refId);
                } else {
                    changeStatusBarStatus("error");
                    changeStatusBarText("Fehler beim Speichern");
                }
            } else {
                changeStatusBarStatus("error");
                changeStatusBarText("Fehler beim Speichern");
            }
        }

    });
}

function deleteObjectImage(id, refId) {
    $.post({
        url: "Services/ObjectService.php",
        data: {
            request: "deleteImage",
            id: id
        },
        success: function(response) {
            var bTry = true;
            try {
                response = JSON.parse(response);
            } catch (e) {
                console.log(e);
                bTry = false;
            }
            if (bTry) {
                if (response.success == true) {
                    editObjectWithId(refId);
                }
            }
        }

    });
}