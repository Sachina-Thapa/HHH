<script>
    function alert(type, msg) {
    let element = document.createElement('div');
    element.className = 'custom-alert'; // Apply the custom alert class
    element.style.backgroundColor = type === 'success' ? 'green' : 'red'; // Set background color based on type
    element.innerHTML = `<strong>${msg}</strong>`;
    
    // Append alert to the body
    document.body.appendChild(element);
    
    // Remove alert after 3 seconds
    setTimeout(() => {
        element.style.opacity = '0'; // Fade out effect
        setTimeout(() => {
            document.body.removeChild(element); // Remove from DOM after fading out
        }, 300); // Wait for fade out to complete before removing
    }, 3000);
    
    console.log("Custom alert added to body"); // Log to confirm
}

        //document.body.append(element);



</script>