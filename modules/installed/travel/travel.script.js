const radioButtons = document.getElementsByName("location-select");
if (radioButtons.length) {
	// We have radio buttons to select, so select the last one the user select
	// by default, or the first one if they havent selected one recently
	for (let i = 0; i < radioButtons.length; i++) {
		const radioButton = radioButtons[i];
		radioButton.checked = (i == 0);
	}

	const btn = document.querySelector('#commit-btn'); 
	if (btn) {
		btn.onclick = function (event) {
			event.preventDefault();
			let index = -1;
			for (let j = 0; j < radioButtons.length; j++) {
				const radioButton = radioButtons[j];
				if (radioButton.checked) {
					const radioButtonIdString = radioButton.id;
					const radioButtonId = radioButtonIdString.replace("location", "");
					index = parseInt(radioButtonId);
					break;
				}
			}
		
			if (index < 0) {
				// No button found selected (should never happen)
				return;
			}

			const locationIndex = ("location=" + index.toString());
			const href = (btn.href + "&" + locationIndex);
			location.href = href;
		};

       /* const tabs = document.querySelectorAll('.tab'); 
        if (tabs) {
            for (let i = 0; i < tabs.length; i++) {
                const tab = tabs[i];
                tab.onclick = function (event) {
                    const currentTarget = event.currentTarget;
                    const tabText = currentTarget.innerHTML;
                    btn.enabled = (tabText === "Reachable");
                }
            }
        }*/
	}
}