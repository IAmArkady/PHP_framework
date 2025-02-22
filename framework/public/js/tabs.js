document.addEventListener('DOMContentLoaded', function () {
    function switchTab(tab) {
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => content.classList.remove('active'));
        const buttons = document.querySelectorAll('.tabs button');
        buttons.forEach(button => button.classList.remove('active'));
        document.querySelector('.' + tab).classList.add('active');
        document.querySelector(`.tabs button[data-tab="${tab}"]`).classList.add('active');
    }

    let tabs = document.getElementsByClassName('tabs')[0];
    let buttons = tabs.getElementsByTagName('button');

    Array.from(buttons).forEach(button => {
        const tabName = button.getAttribute('data-tab');
        button.addEventListener('click', () => {
            switchTab(tabName);
        });
    });
});