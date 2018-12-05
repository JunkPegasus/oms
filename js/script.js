var userCheckBoxList = [];
var Settings;

$(document).ready(function() {
    Settings = JSON.parse(localStorage.getItem("omsSettings"));



    $('.uploadImagesWindow [type=file]').on('change', function() {
        var fileCounter = this.files.length;
        var text = $(this).attr("placeholder");
        if ($(this).val() != "") {
            $(this).next().text(text + " (" + fileCounter + ")");
        } else {
            $(this).next().text(text);
        }

    });

    var data = {
        beforeSubmit: function() {
            showStatusBar("Hochladen...");
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
                    changeStatusBarText("Erfolgreich hochgeladen");
                    editObjectWithId($('#objId').val());
                    closeImageUploadWindow();
                } else {
                    changeStatusBarStatus("error");
                    changeStatusBarText("Fehler Hochladen");
                }
            } else {
                changeStatusBarStatus("error");
                changeStatusBarText("Fehler Hochladen");
            }
        }
    }

    $('#iUpload').ajaxForm(data);

});

function toggleActive(element) {
    $(element).toggleClass("active");
}

function saveSettings() {
    var saveObj = [];
    $('.settingsElement .checkBox').addClass("compute");
    while ($('.checkBox.settings.compute').length > 0) {
        var id = $('.checkBox.settings.compute').attr("data-id");
        var active = $('.checkBox.settings.compute')[0].classList.contains("active");
        saveObj.push({ id: id, active: active });
        console.log($('.checkBox.settings.compute'));
        console.log($('.checkBox.settings.compute').hasClass("active"));
        $('.checkBox.settings.compute')[0].classList.remove("compute");
    }
    $('.nav_menu_subElement').hide();
    saveSettingsRequest(saveObj);
}


function addSettings() {
    if (serverSettings != undefined) {
        element = oTemplates['settingsElement'];
        if (element != "" && element != undefined) {
            var tmp;
            $('.settingsContainer').html("");
            serverSettings.forEach(function(set) {
                tmp = element.replaceAll("%ACTIVE%", (set.isActive == 1) ? "active" : "");
                tmp = tmp.replaceAll("%ID%", set.id);
                tmp = tmp.replaceAll("%TEXT%", set.name);
                $('.settingsContainer').append(tmp);
            });
        }
    }
}

function showUserManagement() {
    if (userList != null) {
        header = oTemplates['userContainerHeader'];
        footer = oTemplates['userContainerFooter'];
        element = oTemplates['userDetailsElement'];
        if (header != undefined && footer != undefined && element != undefined) {
            html = header;
            var tmp;
            var id;
            userList.forEach(function(user) {
                id = Math.trunc(Math.random() * 10000) + "checkbox";
                tmp = element;
                tmp = tmp.replaceAll("%USERID%", user.id);
                tmp = tmp.replaceAll("%CHECKBOXID%", id);
                tmp = tmp.replaceAll("%USERNAME%", user.username);
                tmp = tmp.replaceAll("%SURNAME%", user.surname);
                tmp = tmp.replaceAll("%NAME%", user.name);
                tmp = tmp.replaceAll("%RIGHTS%", user.rights);
                tmp = tmp.replaceAll("%ISADMINALLOWED%", (user.isAdminAllowed == 1) ? 'Ja' : 'Nein');
                tmp = tmp.replaceAll("%DATECREATED%", user.dateCreated);
                tmp = tmp.replaceAll("%USERCREATED%", user.userCreated);
                tmp = tmp.replaceAll("%DATECHANGED%", user.dateChanged);
                tmp = tmp.replaceAll("%USERCHANGED%", user.userChanged);
                html += tmp;
            });
            html += footer;

            $('#main_container').html(html);
        }
    }
}

function removeAllCheckBoxes() {
    $(".checkBox").removeClass("active");
    $('#ulM_deleteBtn').addClass("deactivated");
    userCheckBoxList = [];
}

function toggleUser(userID, checkBoxID) {
    if (!userCheckBoxList.includes(userID)) {
        userCheckBoxList.push(userID);
        $('#' + checkBoxID).addClass("active");
    } else {
        userCheckBoxList.splice(userCheckBoxList.indexOf(userID), 1);
        $('#' + checkBoxID).removeClass("active");
    }
    changeDeleteButtonTextAndState();
}

function changeDeleteButtonTextAndState() {
    if (userCheckBoxList.length > 0) {
        $('#ulM_deleteBtn').removeClass("deactivated");
        $('#ulM_deleteBtn').html("(" + userCheckBoxList.length + ") Ausgewählte Benutzer löschen");
    } else {
        $('#ulM_deleteBtn').addClass("deactivated");
        $('#ulM_deleteBtn').html("(0) Ausgewählte Benutzer löschen");
    }
}

function editUserWithId(userID) {
    $('.uEw_btn').addClass("active");
    $('.userEditWindow').show();
    getUserData(userID);
}

function showUserEditWindow(user) {
    $('.uEw_form').trigger("reset");
    $('.uEw_caption').html("Benutzer bearbeiten");
    $('#uEw_username').val(user.username);
    $('#uEw_surname').val(user.surname);
    $('#uEw_name').val(user.name);
    $('#uEw_rights').val(user.rights);
    $('#uEw_id').val(user.id);
    $('#uEw_id')[0].temp = user.id;
    $('#uEw_isAdminAllowed')[0].checked = ((user.isAdminAllowed == 1) ? true : false);
}

function submitUserEditForm() {
    var un = $('#uEw_username').val();
    var sn = $('#uEw_surname').val();
    var n = $('#uEw_name').val();
    var r = $('#uEw_rights').val();
    var iid = $('#uEw_id').val();
    var iid2 = $('#uEw_id')[0].temp
    var checked = $('#uEw_isAdminAllowed')[0].checked
    var mod = "";
    if (iid == iid2) {
        mod = "edit";
    } else {
        mod = "new";
    }

    var user = {
        username: un,
        surname: sn,
        name: n,
        rights: r,
        id: iid,
        isAdminAllowed: checked,
        mode: mod
    }
    sendUserToServer(user);



    return false;
}



function showStatusBar(message) {
    $('.status_bar_text').html(message);
    $('.status_bar_icon_container').removeClass("error").removeClass("success").addClass("loading");
    $('.status_bar').removeClass("hidden");
}

function changeStatusBarStatus(status) {
    switch (status) {
        case "error":
            $('.status_bar_icon_container').removeClass("success").removeClass("loading").addClass("error");
            break;
        case "loading":
            $('.status_bar_icon_container').removeClass("error").removeClass("success").addClass("loading");
            break;
        case "success":
            $('.status_bar_icon_container').removeClass("error").removeClass("loading").addClass("success");
            break;
    }
    setTimeout(function() {
        $('.status_bar').addClass("hidden");
    }, 2000);
}


function changeStatusBarText(message) {
    $('.status_bar_text').html(message);
}

function closeUserEditWindow() {
    $('.userEditWindow').hide();
    $('.uEw_form').trigger("reset");
}

function toggleUserListMenuBurgerMenu() {
    var menu = $('.ulM_BurgerMenu');
    if (menu.hasClass("active")) {
        menu.removeClass("active");
    } else {
        menu.addClass("active");
    }
}

function showUserCreateWindow() {
    $('.userEditWindow').show();
    $('.uEw_form').trigger("reset");

    $('.uEw_btn').removeClass("active");
    $('.uEw_caption').html("Benutzer erstellen");
    $('#uEw_id').val(99);
    $('#uEw_id')[0].temp = 0;
}

function deleteUserWithID() {
    var ID1 = $('#uEw_id').val();
    var ID2 = $('#uEw_id')[0].temp;

    if (ID1 == ID2) {
        deleteUserRequest(ID1);
    }
}

function showDeleteMultipleUser() {
    if (userCheckBoxList.length > 0) {
        if (userList != null) {
            template = oTemplates['deleteUserElement'];
            if (template != "") {
                var id;
                var tmp;
                var html = "";
                var statusIdList = [];
                userCheckBoxList.forEach(function(uid) {
                    id = Math.trunc(Math.random() * 10000) + "loading";
                    user = findUserToId(uid);
                    if (user != false) {
                        tmp = template;
                        tmp = tmp.replaceAll("%ID%", id);
                        statusIdList.push(id);
                        tmp = tmp.replaceAll("%USERNAME%", user.username);
                        html += tmp;
                    }
                });

                $('.dMW_elementList').html(html);
                $('.deleteMultipleWindow').show();
                $('.deleteMultipleWindow')[0].statusIdList = statusIdList;
                $('.deleteMultipleWindow')[0].idList = userCheckBoxList;
                $('.deleteMultipleWindow')[0].deleteAuth = true;
            }
        }
    }
}

function deleteMultipleUser() {
    if ($('.deleteMultipleWindow')[0].deleteAuth) {
        var statusIdList = $('.deleteMultipleWindow')[0].statusIdList;
        var idList = $('.deleteMultipleWindow')[0].idList;
        if (statusIdList != undefined && idList != undefined) {
            if (statusIdList.length == idList.length) {
                for (var i = 0; i < statusIdList.length; i++) {
                    $('.deleteMultipleWindow')[0].deleteAuth = false;
                    removeAllCheckBoxes();
                    deleteUserWithStatus(idList[i], statusIdList[i]);
                }
            }
        }
    }
}

function findUserToId(uid) {
    for (var i = 0; i < userList.length; i++) {
        if (userList[i].id == uid) return userList[i];
    }
    return false;
}

function closeUserDeleteWindow() {
    $('.deleteMultipleWindow').hide();
    $('.dMW_elementList').html("");
}

function showObjectManagement() {
    if (objectList != null) {
        header = oTemplates['objectsContainerHeader'];
        footer = oTemplates['objectsContainerFooter'];
        element = oTemplates['objectsElement'];
        if (header != undefined && footer != undefined && element != undefined) {
            html = header;
            var tmp;
            objectList.forEach(function(object) {
                tmp = element;
                tmp = tmp.replaceAll("%ID%", object.id);
                tmp = tmp.replaceAll("%NAME%", object.name);
                tmp = tmp.replaceAll("%VIEWS%", object.views);
                tmp = tmp.replaceAll("%DATECREATED%", object.dateCreated);
                tmp = tmp.replaceAll("%USERCREATED%", object.userCreated);
                tmp = tmp.replaceAll("%DATECHANGED%", object.dateChanged);
                tmp = tmp.replaceAll("%USERCHANGED%", object.userChanged);
                html += tmp;
            });
            html += footer;

            $('#main_container').html(html);
        }
    }
}

function showObjectSubManagement(id, name) {
    if (objectSubList != null) {
        header = oTemplates['objectListContainerHeader'];
        footer = oTemplates['objectListContainerFooter'];
        element = oTemplates['objectListElement'];
        if (header != undefined && footer != undefined && element != undefined) {
            html = header.replaceAll("%OBJECTID%", id);
            html = html.replaceAll("%OBJECTNAME%", name);
            var tmp;
            objectSubList.forEach(function(object) {
                tmp = element;
                tmp = tmp.replaceAll("%ID%", object.id);
                tmp = tmp.replaceAll("%NAME%", object.name);
                tmp = tmp.replaceAll("%VIEWS%", object.views);
                tmp = tmp.replaceAll("%PUBLIC%", (object.public == 1 ? "public" : "internal"));
                tmp = tmp.replaceAll("%DATECREATED%", object.dateCreated);
                tmp = tmp.replaceAll("%USERCREATED%", object.userCreated);
                tmp = tmp.replaceAll("%DATECHANGED%", object.dateChanged);
                tmp = tmp.replaceAll("%USERCHANGED%", object.userChanged);
                html += tmp;
            });
            html += footer;

            $('#main_container').html(html);
        }
    }
}

function editObjectWithId(id) {
    loadObjectData(id);
}

function showEditObject() {
    if (oEditableObject != null) {
        header = oTemplates['objectEditHeader'];
        footer = oTemplates['objectEditFooter'];
        fieldElementText = oTemplates['objectEditFieldText'];
        fieldElementNumber = oTemplates['objectEditFieldNumber'];
        fieldElementTextarea = oTemplates['objectEditFieldTextarea'];
        imageElement = oTemplates['objectEditImage'];
        seperator = oTemplates['objectEditFieldImageSeperator'];

        if (header != "" && footer != "" && fieldElementText != "" && fieldElementNumber != "" && fieldElementTextarea != "" && imageElement != "" && seperator != "") {
            html = header.replaceAll("%GLOBAL_PUBLIC%", (oEditableObject.object.public == 1 ? "public" : ""));
            html = html.replaceAll("%OBJECT_NAME%", oEditableObject.object.name);
            html = html.replaceAll("%REF_ID%", oEditableObject.object.refId);
            html = html.replaceAll("%OBJECT_ID%", oEditableObject.object.id);
            html = html.replaceAll("%REF_NAME%", oEditableObject.object.refName);
            html = html.replaceAll("%HIDDEN_IMAGE%", (oEditableObject.object.hasImages == 0 ? "hidden" : ""));
            var tmp;

            if (oEditableObject.object.hasFields == "1") {
                oEditableObject.fields.forEach(function(field) {
                    switch (field.type) {
                        case "text":
                            tmp = fieldElementText;
                            tmp = replaceFieldElementVariables(field, tmp);
                            break;
                        case "number":
                            tmp = fieldElementNumber;
                            tmp = replaceFieldElementVariables(field, tmp);
                            break;
                        case "textarea":
                            tmp = fieldElementTextarea;
                            tmp = replaceFieldElementVariables(field, tmp);
                            break;
                        default:
                            tmp = "Wrong Field Type";
                    }
                    html += tmp;
                });
            }

            html += seperator;

            if (oEditableObject.object.hasImages == 1) {
                oEditableObject.images.forEach(function(image) {
                    tmp = imageElement.replaceAll("%IMAGE_INTERNAL%", (image.internal == 1 ? "internal" : ""));
                    tmp = tmp.replaceAll("%IMAGE_PUBLIC%", (image.public == 1 ? "public" : ""));
                    tmp = tmp.replaceAll("%IMAGE_COVER%", (image.isCoverImage == 1 ? "cover" : ""));
                    tmp = tmp.replaceAll("%IMAGE_SRC%", image.path);
                    tmp = tmp.replaceAll("%IMAGE_ID%", image.id);
                    tmp = tmp.replaceAll("%REF_ID%", image.refId);

                    html += tmp;
                });
            }

            html += footer;
            $('#main_container').html(html);
        }
    }
}


function replaceFieldElementVariables(field, template) {
    template = template.replaceAll("%FIELD_WIDTH%", field.maxLength / 2);
    template = template.replaceAll("%FIELD_NAME%", field.name);
    template = template.replaceAll("%FIELD_ID%", field.id);
    template = template.replaceAll("%FIELD_PLACEHOLDER%", field.name);
    template = template.replaceAll("%FIELD_VALUE%", (field.value == null ? "" : field.value));
    template = template.replaceAll("%MAX_LENGTH%", field.maxLength);
    template = template.replaceAll("%MIN_LENGTH%", field.minLength);

    return template;
}

function closeCreateObjectWindow() {
    $('.createObjectWindow').remove();
}

function openCreateObjectWindow(id, name) {
    template = oTemplates['objectCreateWindow'];
    if (template != "" || template != undefined) {
        template = template.replaceAll("%OBJECT_NAME%", name);
        template = template.replaceAll("%OBJECT_REFID%", id);

        $('body').append(template);
    }
}

function saveObjectFields(refId) {
    var id;
    var value;
    var fields = [];
    for (var i = 0; i < $('.objectEditFieldInput').length; i++) {
        id = 0;
        id = $('.objectEditFieldInput')[i].attributes.getNamedItem("data-id").value;
        value = $('.objectEditFieldInput')[i].value;
        fields.push({ id: id, value: value, refId: refId });
    }
    saveFieldDataRequest(fields)
}


function uploadImages(element) {
    console.log($(element));

    return false;
}

function closeImageUploadWindow() {
    $('.uploadImagesWindow').hide();
}

function showImageUploadWindow(id) {
    $('#iUpload').resetForm();
    $('#objId').val(id);
    $('.uploadImagesWindow').show();
}

function saveLocalSettings() {
    localStorage.setItem("omsSettings", JSON.stringify(Settings));
}

function addChangelog() {
    var element = oTemplates['changelog'];
    if (element != "" && element != undefined && changeLog != undefined) {
        $('#changelogList').html("");
        changeLog.forEach(function(change) {
            tmp = element.replaceAll("%NEW%", (change.new == 1) ? "new" : "");
            tmp = tmp.replaceAll("%DATE%", change.date.split(" ")[0]);
            tmp = tmp.replaceAll("%TYPE%", (change.type == 0) ? "Changelog V" + change.version : "Website Änderung");
            tmp += "<ul>";
            var texts = change.text.split("%%");
            texts.forEach(function(text) {
                if (text != "") {
                    tmp += "<li>" + text + "</li>";
                }
            });
            tmp += "</ul>";
            $('#changelogList').append(tmp);
        });
    }
}

function closeTarget(target) {
    $(target).hide();
}

function showTarget(target) {
    $(target).show();
}

function showChangelog() {
    $('.changeLogWindow').show();
    $('.nav_menu_button.newIndicator').removeClass("newIndicator");
}

function checkChangeLog() {
    changeLog.forEach(function(change) {
        if (change.new == "1") {
            $('#changelogToggle').addClass("newIndicator");

        }
    });
}

function checkNavigation() {
    var hash = location.hash;
    if (hash != "") {
        hashArr = hash.split("&");
        if (hashArr.length < 2 && hashArr[0] != "") {
            hashArr = hashArr[0].split("=");
            if (hashArr.length == 2) {
                var content = hashArr[1];
                if (content != "") {
                    switch (content) {
                        case "obj":
                            getObjectList();
                            break;
                        case "user":
                            getUserList(2);
                            break;
                    }
                }
            }
        } else if (hashArr.length == 2) {
            var page = hashArr[0].split("=")[1];
            var content = hashArr[1].split("=")[1];
            if (page == "objEd") {
                content = parseInt(content);
                if (content != NaN) {
                    editObjectWithId(content);
                }
            }
        } else if (hashArr.length == 3) {
            var page = hashArr[0].split("=")[1];
            var content = hashArr[1].split("=")[1];
            var name = hashArr[2].split("=")[1];
            if (page == "objLi") {
                content = parseInt(content);
                if (content != NaN) {
                    getObjectSubList(content, name);
                }
            }
        }
    }
}