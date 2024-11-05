// Setze diese Variable auf true oder false, um die Captcha-Überprüfung zu aktivieren oder zu deaktivieren
const captchaRequired = false; // Ändere auf true, um die Überprüfung zu aktivieren

// Hauptfunktion zur Formularvalidierung
function validateForm() {
    // Eingabewerte holen und Leerzeichen entfernen
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const passwordRepeat = document.getElementById("password_repeat").value.trim();
    const name = document.getElementById("name").value.trim();
    const city = document.getElementById("city").value.trim();
    const country = document.getElementById("country").value.trim();
    const age = document.getElementById("age").value.trim();
    const termsAccepted = document.getElementById("accept_terms").checked;
    const captchaResponse = document.querySelector("[name='h-captcha-response']").value;

    // Reguläre Ausdrücke für die Validierung definieren
    const nameRegex = /^[A-Za-z\s]+$/; // Nur Buchstaben und Leerzeichen erlaubt
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Standard E-Mail-Format
    const ageRegex = /^[0-9]+$/; // Nur numerische Zeichen für das Alter

    let valid = true; // Variable zur Überprüfung der Gesamtvalidität des Formulars

    // Validierung des Benutzernamens
    if (username.length < 3) {
        showError("username", "Benutzername muss mindestens 3 Zeichen lang sein");
        valid = false;
    } else {
        clearError("username");
    }

    // Validierung der E-Mail-Adresse
    if (!emailRegex.test(email)) {
        showError("email", "Bitte eine gültige E-Mail-Adresse eingeben");
        valid = false;
    } else {
        clearError("email");
    }

    // Validierung des Passworts
    if (password.length < 6) {
        showError("password", "Passwort muss mindestens 6 Zeichen lang sein");
        valid = false;
    } else {
        clearError("password");
    }

    // Überprüfung, ob die Passwörter übereinstimmen
    if (password !== passwordRepeat) {
        showError("password_repeat", "Passwörter müssen übereinstimmen");
        valid = false;
    } else {
        clearError("password_repeat");
    }

    // Validierung des Namens (nur Buchstaben und Leerzeichen erlaubt)
    if (!nameRegex.test(name)) {
        showError("name", "Name darf nur Buchstaben und Leerzeichen enthalten");
        valid = false;
    } else {
        clearError("name");
    }

    // Validierung des Wohnorts (nur Buchstaben und Leerzeichen erlaubt)
    if (!nameRegex.test(city)) {
        showError("city", "Wohnort darf nur Buchstaben und Leerzeichen enthalten");
        valid = false;
    } else {
        clearError("city");
    }

    // Validierung des Landes (nur Buchstaben und Leerzeichen erlaubt)
    if (!nameRegex.test(country)) {
        showError("country", "Land darf nur Buchstaben und Leerzeichen enthalten");
        valid = false;
    } else {
        clearError("country");
    }

    // Validierung des Alters (muss eine Zahl zwischen 1 und 120 sein)
    if (!ageRegex.test(age) || parseInt(age) < 1 || parseInt(age) > 120) {
        showError("age", "Bitte ein gültiges Alter zwischen 1 und 120 eingeben");
        valid = false;
    } else {
        clearError("age");
    }

    // Überprüfen, ob das Captcha ausgefüllt ist, falls captchaRequired auf true gesetzt ist
    if (captchaRequired && !captchaResponse) {
        valid = false;
    }

    // Button aktivieren oder deaktivieren, abhängig von der Gesamtvalidität und den Akzeptanzbedingungen
    document.getElementById("register-button").disabled = !(valid && termsAccepted && (!captchaRequired || captchaResponse));
}

// Funktion zum Anzeigen eines Fehlers für ein bestimmtes Feld
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    field.classList.add("error");  // Roter Rahmen hinzufügen
    field.setAttribute("title", message);  // Setzt den Tooltip als Fehlermeldung
}

// Funktion zum Entfernen eines Fehlers für ein bestimmtes Feld
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    field.classList.remove("error");  // Roten Rahmen entfernen
    field.removeAttribute("title");  // Entfernt den Tooltip, wenn keine Fehlermeldung mehr vorhanden ist
}

// Event-Listener für Änderungen an den Eingabefeldern und dem AGB-Kontrollkästchen hinzufügen
document.querySelectorAll("#username, #email, #password, #password_repeat, #name, #city, #country, #age, #accept_terms")
    .forEach(element => {
        element.addEventListener("input", validateForm); // Bei jeder Änderung wird validateForm aufgerufen
    });

// Zusätzlicher Event-Listener für hCaptcha, falls aktiviert
if (captchaRequired) {
    document.querySelector("[name='h-captcha-response']").addEventListener("input", validateForm);
}

// Initiale Validierung ausführen, um sicherzustellen, dass der Button korrekt deaktiviert ist
validateForm();
