"use strict";

let write = true;

async function sendFetchRequest(method, url, data = null, contentType = "application/json") {
    const noBodyMethods = new Set(["GET", "HEAD"]);
    const headers = new Headers();
    let body = undefined;

    if (method === "GET" && data) {
        url += `?${new URLSearchParams(data).toString()}`;
    } else if (!noBodyMethods.has(method)) {
        headers.append("Content-Type", contentType);
        switch (contentType) {
            case "application/json":
                body = JSON.stringify(data);
                break;
            case "application/x-www-form-urlencoded":
                body = new URLSearchParams(data).toString();
                break;
            case "multipart/form-data":
                body = data;
                headers.delete("Content-Type");
                break;
            case "text/plain":
                body = data;
                break;
            default:
                body = data;
        }
    }

    try {
        const response = await fetch(url, { method, headers, body });
        if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        return response.text();
    } catch (error) {
        console.error("Fetch Error:", error);
        throw error;
    }
}

function getErrorText(text, errorChars) {
    return text.split('').map((char, index) => {
        if (errorChars.includes(char.toLowerCase())) {
            return `<span class="error">${char}</span>`;
        }
        return char;
    }).join('');
}

async function checkLanguage(writeDB) {
    const form = document.querySelector("form");
    const textarea = form.querySelector(".textarea");

    try {
        const data = await sendFetchRequest("POST", form.action, { text: textarea.value, write: writeDB });
        const parsedData = JSON.parse(data);
        document.getElementById("highlight").innerHTML = getErrorText(textarea.value, parsedData.error_chars);
        if (write) write = false;
    } catch (error) {
        console.error("Error:", error);
    }
}

async function getHistory() {
    const form = document.querySelector("form");
    try {
        const data = await sendFetchRequest("GET", form.action);
        const result = JSON.parse(data).history;
        const historyDiv = document.querySelector(".history");
        historyDiv.innerHTML = "";
        result.forEach(line => {
            historyDiv.insertAdjacentHTML("beforeend", `<div class="history-text">${getErrorText(line.text, line.error_chars)}</div>`);
        });
    } catch (error) {
        console.error("Error:", error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelector(".check-button").addEventListener("click", event => {
        event.preventDefault();
        checkLanguage(true);
    });

    document.getElementById("history").addEventListener("click", event => {
        event.preventDefault();
        getHistory();
    });

    document.querySelector(".textarea").addEventListener("input", () => {
        if (!write) checkLanguage(false);
    });
});
