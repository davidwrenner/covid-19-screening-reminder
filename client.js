"use strict";

function submitRegistration() {
    let number = document.getElementById("phone-number").value;
    let time = document.getElementById("time").value;

    // condense days of the week to a single bit string
    let days = "";
    for (let i = 0; i < 7; ++i) {
        days += document.getElementById("day" + i).checked ? "1" : "0";
    }

    const data = {
        'number' : number,
        'time' : time,
        'days' : days
    };

    fetch("registerNumber.php", {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'content-type': 'application/json' }
        })
    .then(result => result.json())
    .then(data => {
        if(data.success){
            let inner = document.getElementsByClassName("inner-container");
            inner[0].textContent = "Thanks! You've successfully registered for daily screening reminders.";
        }
        else{
            alert("Unable to register with the provided information.");
        }
    })
    .catch(err => console.error(err));

}

document.getElementById("submit-reg-btn").addEventListener("click", function(e){
    e.preventDefault();
    submitRegistration();
});
