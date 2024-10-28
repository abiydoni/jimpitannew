const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item=> {
	const li = item.parentElement;

	item.addEventListener('click', function () {
		allSideMenu.forEach(i=> {
			i.parentElement.classList.remove('active');
		})
		li.classList.add('active');
	})
});

// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
})

// const switchMode = document.getElementById('switch-mode');

// switchMode.addEventListener('change', function () {
// 	if(this.checked) {
// 		document.body.classList.add('dark');
// 	} else {
// 		document.body.classList.remove('dark');
// 	}
// })
    // Get the switch mode checkbox
    const switchMode = document.getElementById('switch-mode');

    // Check local storage for the mode preference
    if (localStorage.getItem('mode') === 'dark') {
        document.body.classList.add('dark'); // Add dark mode class to body
        switchMode.checked = true; // Set the checkbox as checked
    }

    // Event listener for the switch
    switchMode.addEventListener('change', () => {
        if (switchMode.checked) {
            document.body.classList.add('dark');
            localStorage.setItem('mode', 'dark'); // Save preference to local storage
        } else {
            document.body.classList.remove('dark');
            localStorage.setItem('mode', 'light'); // Save preference to local storage
        }
    });
