<?php
include_once 'Common/UserInfo.php';
if (!userLoggedIn()) {
    header('Location: index.php?login=false');
}
?>
    <!DOCTYPE html>
    
    <html>

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>OMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400|Roboto:700,900" rel="stylesheet">
        <link rel="stylesheet" type="text/css" media="screen" href="css/style.css" />
         <script src="js/jquery.js"></script>
        <script src="js/ajax.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
        <script src="js/script.js"></script>

    </head>

    <body>
        <div class="nav">
            <div class="nav_left">
                <div class="nav_header">
                    <h2>OMS</h2>
                </div>
                <div class="nav_menu">
                    <div class="nav_menu_button"onclick="getObjectList()">Objekte</div>
                    <div class="nav_menu_button" onclick="getUserList(2)">Benutzer</div>
                    <div class="nav_menu_button">Login-Log</div>
                    <div class="nav_menu_button newIndicator" onclick="showChangelog()">Changelog</div>
                </div>
            </div>
            <div class="nav_right">
                <div class="nav_username"><?php echo $_SESSION['userName'] ?></div>
                <div class="nav_menu">
                    <div class="nav_menu_icon">
                        <a href="Services/LogoutService.php">
                            <img src="images/icons/SVG/Icon_Logout.svg">
                        </a>
                    </div>
                    <div class="nav_menu_icon">
                        <img src="images/icons/SVG/Icon_User.svg">
                    </div>
                    <div class="nav_menu_icon" onclick="$('.nav_menu_subElement.right').toggle()">
                        <img src="images/icons/SVG/Icon_Settings.svg"> 
                    </div>
                </div>
                <div class="nav_menu_subElement right" >
                    <div class="settingsContainer"></div>
                    <div class="settingsElement">
                        <div class="saveSettingsButton" onclick="saveSettings()">Speichern</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="main_container">

        </div>


        <div class="userEditWindow">
            <div class="uEw_header">
                <div class="uEw_caption">Benutzer bearbeiten</div>
                <div class="uEw_closeBtn" onclick="closeUserEditWindow()">
                    <img src="images/icons/SVG/Icon_Close_White.svg">
                </div>
            </div>
            <div class="uEw_content">
                <form class="uEw_form" onsubmit="return submitUserEditForm()" method="post" target="Services/UserService.php">
                    <div class="uEw_form_field">
                        <div class="uEw_form_field_caption">Benutzername</div>
                        <input id="uEw_username" type="text" name="username" maxlength=30 minlength=4 placeholder="Benutzername" required>
                    </div>
                    <div class="uEw_form_field">
                        <div class="uEw_form_field_caption">Vorname</div>
                        <input id="uEw_surname" type="text" name="surname" placeholder="Vorname" required>
                    </div>
                    <div class="uEw_form_field">
                        <div class="uEw_form_field_caption">Nachname</div>
                        <input id="uEw_name" type="text" name="name" placeholder="Nachname" required>
                    </div>
                    <div class="uEw_form_field">
                        <div class="uEw_form_field_caption">Rechte</div>
                        <input id="uEw_rights" type="number" name="rights" max=10 min=0 value=0 required>
                        <i>0 = keine Rechte; 10 = max. Rechte | Rechte sind nicht gleich Adminrechte!</i>
                    </div>
                    <div class="uEw_form_field">
                        <div class="uEw_form_field_caption">Adminrechte</div>
                        <input id="uEw_isAdminAllowed" type="checkbox" name="isAdminAllowed">
                    </div>
                    <div class="uEw_footer">
                        <div class="uEw_btn active" id="deleteUserBtn" onclick="deleteUserWithID()">Löschen</div>
                        <div class="uEw_btn" onclick="closeUserEditWindow()">Abbrechen</div>
                        <input type="submit" value="Speichern">
                        <input id="uEw_id" name="id" type="hidden" required>
                    </div>
                </form>
            </div>
        </div>
        <div class="status_bar hidden">
            <div class="status_bar_text">Speichern..</div>
            <div class="status_bar_icon_container loading">
                <div class="status_bar_icon loading">
                    <div class="status_bar_icon_loading"></div>
                </div>
                <div class="status_bar_icon success">
                    <div class="status_bar_icon_success">
                        <div class="suc1"></div>
                        <div class="suc2"></div>
                    </div>
                </div>
                <div class="status_bar_icon error">
                    <div class="err_cont">
                        <div class="err1"></div>
                        <div class="err2"></div>
                    </div>
			</div>
		</div>
        </div>
        <div class="deleteMultipleWindow">
            <div class="dMW_header">
                <div class="dmW_caption">Bestätigen</div>
                <div class="dmW_closeBtn" onclick="closeUserDeleteWindow()">
                    <img src="images/icons/SVG/Icon_Close_White.svg">
                </div>
            </div>
            <div class="dMW_content">
                <div class="dMW_text">Wollen Sie diese Benutzer wirklich löschen?</div>
                <ul class="dMW_elementList">
                </ul>
            </div>
            <div class="dMW_footer">
                <div class="dMW_btn" onclick="closeUserDeleteWindow()">Schließen</div>
                <div class="dMW_btn" onclick="deleteMultipleUser()">Löschen</div>
            </div>
        </div>

        <div class="uploadImagesWindow">
            <div class="windowHeader">
                <div class="windowheaderCaption">Bilder hochladen</div>
                <div class="windowheaderCloseBtn" onclick="closeImageUploadWindow()">
                    <img src="images/icons/SVG/Icon_Close_White.svg">
                </div>
            </div>
            <div class="windowContent">
                <form action="Services/ImageUploadService.php" id="iUpload" method="post">
                    <input id="iI" type="file" name="internalImages[]" multiple accept=".png, .jpg, .jpeg" placeholder="Interne Bilder"/>
                    <label for="iI">Interne Bilder</label>
                    <input id="pI" type="file" name="publicImages[]" required multiple accept=".png, .jpg, .jpeg" placeholder="Öffentliche Bilder"/>
                    <label for="pI">Öffentliche Bilder</label>
                    <input type="hidden" name="request" value="uploadImages">
                    <input type="hidden" name="objId" id="objId" value=1>
                    <input type="submit" value="Hochladen">
                </form>
            </div>
        </div>


        <div class="changeLogWindow">
            <div class="windowHeader">
                <div class="windowheaderCaption">Changelog</div>
                <div class="windowheaderCloseBtn" onclick="closeTarget('.changeLogWindow')">
                    <img src="images/icons/SVG/Icon_Close_White.svg">
                </div>
            </div>
            <div class="changeLogWindowContent">
                <ul class="list" id="changelogList">
                    
                </ul>
            </div>
        </div>
    </body>

    </html>