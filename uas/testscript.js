/*
 * Calculates password entropy and displays it for the user to see.
 * Adapted from http://blog.shay.co/password-entropy/
 */
$(document).ready(function() {
    function calculateAlphabetSize(password) {
        var alphabet = 0, lower = false, upper = false, numbers = false, symbols1 = false, symbols2 = false, other = '', c;

        for(var i = 0; i < password.length; i++) {
            c = password[i];
            if(!lower && 'abcdefghijklmnopqrstuvwxyz'.indexOf(c) >= 0) {
                alphabet += 26;
                lower = true;
            }
            else if(!upper && 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.indexOf(c) >= 0) {
                alphabet += 26;
                upper = true;
            }
            else if(!numbers && '0123456789'.indexOf(c) >= 0) {
                alphabet += 10;
                numbers = true;
            }
            else if(!symbols1 && '!@#$%^&*()'.indexOf(c) >= 0) {
                alphabet += 10;
                symbols1 = true;
            }
            else if(!symbols2 && '~`-_=+[]{}\\|;:\'",.<>?/'.indexOf(c) >= 0) {
                alphabet += 22;
                symbols2 = true;
            }
            else if(other.indexOf(c) === -1) {
                alphabet += 1;
                other += c;
            }
        }		
        return alphabet;
    }

    function calculateEntropy(password) {
        if(password.length === 0) return 0;
        var entropy = password.length * Math.log(calculateAlphabetSize(password)) / Math.log(2);
        return Math.round((Math.round(entropy * 100) / 100));
    }

    $('#password input').keyup(function() {
        $('#pwstr span').html(calculateEntropy($(this).val()));
        if(calculateEntropy($(this).val()) > 100) {
            document.getElementById('pwstr').firstChild.style.color = '#66cc66';
        } else {
            document.getElementById('pwstr').firstChild.style.color = '#ff6666';
        }
        
    });
    
});

/*
 * Shows if the confirm password box matches the password box.
 */
function checkPass() {
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');

    if(pass1.value == pass2.value) {
        document.getElementById('match').innerHTML = "Password fields match!";
        document.getElementById('match').style.color = '#66cc66';
    } else {
        document.getElementById('match').innerHTML = "Password fields must match.";
        document.getElementById('match').style.color = '#ff6666';
    }
    
    
}

/*
 * Changes the color on the password entropy text.
function checkEntropy() {
    var entropy = document.getElementById('pwent').firstChild;
    
    if(entropy > 100) {
        document.getElementById('pwstr').style.color = '#66cc66';
    } else {
        document.getElementById('pwstr').style.color = '#ff6666';
    }

}*/