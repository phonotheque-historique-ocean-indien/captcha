<?php
/*
                      &
                   %&&&                               (&&
                    &&&                                &&
                    &&&
                    &&&    *                             %
                    &&&&&/ *&&&&                     &&&
                    &&&      &&&                      ,&&.
                    &&&      /&&                      .&&
    &&,       &&&                   &&&        &&&
    &&,      /&&,                   &&&        &&&
    &&&     .&&                      .&&      &&&
    &&,&&&&&                            *&&&
    &&,
    &&,
   ,&&&

   PHOI Phonothèque Historique de l'Océan - CAPTCHA

   Créé par idéesculture (G. Michelin) - 2019
   Modifié par :
        (ajouter vos NOMS - DATES ici, 1 ligne par contributeur)

   This project is GNU GPL v3. https://www.gnu.org/licenses/gpl-3.0.html

   -----------------------------------------------------------------------------
   Images and icons in this project are CC BY NC SA if not elsewise documented.
   If you are using this project inside a commercial project, please do your own
   graphism and publish the sources, mandatory with GNU GPL v3 license. Thanks.
   -----------------------------------------------------------------------------
   index.php
*/
session_start();

// If we have a valid POST and valid session captcha informations
if($_POST && is_array(json_decode($_SESSION['captcha_position_click']))) {
    $captcha_info = json_decode($_SESSION['captcha_position_click']);
    $size=$captcha_info[0];
    $angle=$captcha_info[1];
    $x=$captcha_info[2];
    $y=$captcha_info[3];
    $captcha_challenge_x = filter_var($_POST["captcha_challenge_x"], FILTER_SANITIZE_NUMBER_INT);
    $captcha_challenge_y = filter_var($_POST["captcha_challenge_y"], FILTER_SANITIZE_NUMBER_INT);

    // Pythagorus : test if a point is inside a circle, (x-center_x)^2 + (y - center_y)^2 < radius^2
    // Reminder : $num ** 2 == $num ^2

    if ((($captcha_challenge_x - $x)**2) + (($captcha_challenge_y - $y)**2) < ($size ** 2)) {
        // IF CAPTCHA IS VALID...
        $visitor_name = "";
        $visitor_email = "";
        $email_title = "";
        $concerned_department = "";
        $visitor_message = "";

        if (isset($_POST['visitor_name'])) {
            $visitor_name = filter_var($_POST['visitor_name'], FILTER_SANITIZE_STRING);
        }

        if (isset($_POST['visitor_email'])) {
            $visitor_email = str_replace(array("\r", "\n", "%0a", "%0d"), '', $_POST['visitor_email']);
            $visitor_email = filter_var($visitor_email, FILTER_VALIDATE_EMAIL);

        }

        if (isset($_POST['email_title'])) {
            $email_title = filter_var($_POST['email_title'], FILTER_SANITIZE_STRING);
        }

        if (isset($_POST['concerned_department'])) {
            $concerned_department = filter_var($_POST['concerned_department'], FILTER_SANITIZE_STRING);
        }

        if (isset($_POST['visitor_message'])) {
            $visitor_message = htmlspecialchars($_POST['visitor_message']);
        }

        if ($concerned_department == "billing") {
            $recipient = "gm@ideesculture.com";
        } else if ($concerned_department == "marketing") {
            $recipient = "gm@ideesculture.com";
        } else if ($concerned_department == "technical support") {
            $recipient = "gm@ideesculture.com";
        } else {
            $recipient = "gm@ideesculture.com";
        }

        $headers = 'MIME-Version: 1.0' . "\r\n"
            . 'Content-type: text/html; charset=utf-8' . "\r\n"
            . 'From: ' . $visitor_email . "\r\n";

        if (mail($recipient, $email_title, $visitor_message, $headers)) {
            echo "<p>Thank you for contacting us, $visitor_name. You will get a reply within 24 hours.</p>";
        } else {
            echo '<p>We are sorry but the email did not go through.</p>';
        }
    } else {
        // IF CAPTCHA IS INVALID...
?>
        <p>Your captcha is invalid.</p>
        <p>You will redirected to the contact form.</p>
        <script>
            window.setTimeout( function() {
                window.location.href = ".";
            }, 500);
        </script>
<?php
        // Captcha invalid, don't do anything more.
        exit();

        // END IF CAPTCHA IS INVALID...
    }
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<form action="index.php" method="post">
    <div class="elem-group">
        <label for="name">Your Name</label>
        <input type="text" id="name" name="visitor_name" placeholder="John Doe" pattern=[A-Z\sa-z]{3,20} required>
    </div>
    <div class="elem-group">
        <label for="email">Your E-mail</label>
        <input type="email" id="email" name="visitor_email" placeholder="john.doe@email.com" required>
    </div>
    <div class="elem-group">
        <label for="department-selection">Choose Concerned Department</label>
        <select id="department-selection" name="concerned_department" required>
            <option value="">Select a Department</option>
            <option value="billing">Billing</option>
            <option value="marketing">Marketing</option>
            <option value="technical support">Technical Support</option>
        </select>
    </div>
    <div class="elem-group">
        <label for="title">Reason For Contacting Us</label>
        <input type="text" id="title" name="email_title" required placeholder="Unable to Reset my Password" pattern=[A-Za-z0-9\s]{4,60}>
    </div>
    <div class="elem-group">
        <label for="message">Write your message</label>
        <textarea id="message" name="visitor_message" placeholder="Say whatever you want." required></textarea>
    </div>
    <div class="elem-group">
        <label for="captcha">On this image, only one circle is not a closed one. Please click on it.</label>
        <div style="text-align:right;"><span class="refresh-captcha" style="line-height:20px;"><img style="margin-bottom:-4px;border:none;" src="reload.svg" />Can't find the opened circle ? Generate another captcha.</span></div>
        <img src="captcha.php" alt="CAPTCHA" class="captcha-image" />
        <div id="captcha-message"></div>
        <br>
        <input type="hidden" id="captcha-x" name="captcha_challenge_x">
        <input type="hidden" id="captcha-y" name="captcha_challenge_y">
    </div>
    <button id="submit" class="disabled" type="submit" disabled="disabled">Send Message</button>
</form>


<script>
    $(document).ready(function() {
        $("img.captcha-image").on("click", function(event) {
            var x = event.pageX - this.offsetLeft;
            var y = event.pageY - this.offsetTop;
            $("#captcha-x").val(x);
            $("#captcha-y").val(y);
            $("#captcha-message").html("Your click position has been recorded. <small>["+x+","+y+"]</small>");
            $("#submit").removeClass("disabled");
            $("#submit").removeAttr("disabled");
        });
    });

    var refreshButton = document.querySelector(".refresh-captcha");
    refreshButton.onclick = function() {
        document.querySelector(".captcha-image").src = 'captcha.php?' + Date.now();
    }
</script>

<style>
    form {
        width:600px;
    }
    div.elem-group {
        margin: 40px 0;
    }

    label {
        display: block;
        font-family: 'Aleo';
        padding-bottom: 4px;
        font-size: 1.25em;
    }

    input, select, textarea {
        border-radius: 2px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 1.25em;
        font-family: 'Aleo';
        width: 500px;
        padding: 8px;
    }

    textarea {
        height: 250px;
    }

    button {
        height: 50px;
        background: green;
        color: white;
        font-size: 1.25em;
        font-family: 'Aleo';
        border-radius: 4px;
        cursor: pointer;
    }
    button.disabled {
        background-color: gray;
        color:lightgray;
    }
    button:hover {
        border: 2px solid black;
    }
    .elem-group img {
        border:1px solid lightgray;
        margin-bottom: 10px;
    }
    #captcha-message small {
        color:gray;
    }

    .refresh-captcha {
        color:gray;
    }
</style>