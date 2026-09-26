import { useState } from "react";

function PasswordValidator() {

 const [password, setPassword] = useState("");
    const [message, setMessage] = useState("");

    function validatePassword() {

        if (password.length < 8) {
            setMessage("Password must contain at least 8 characters.");
        }
        else if (!/[A-Z]/.test(password)) {
            setMessage("Password must contain at least one uppercase letter.");
        }
        else if (!/[a-z]/.test(password)) {
            setMessage("Password must contain at least one lowercase letter.");
        }
        else if (!/[0-9]/.test(password)) {
            setMessage("Password must contain at least one number.");
        }
        else if (!/[!@#$%^&*]/.test(password)) {
            setMessage("Password must contain at least one special character.");
        }
        else {
            setMessage("Password is valid!");
        }
    }

    return (
        <div>

            <h1>Password Validator</h1>

            <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Enter password"
            />

            <br /><br />

            <button onClick={validatePassword}>
                Validate Password
            </button>

            <h2>{message}</h2>

        </div>
    );
}

export default PasswordValidator;