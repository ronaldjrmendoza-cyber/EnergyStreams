/* for playing the audio broadcasts */
let currentAudio = null;
let currentIcon = null;

function togglePlay(id) {
    const audio = document.getElementById('audio-' + id);
    const icon = document.getElementById('icon-' + id);

    // if another audio is playing, stop it and reset icon
    if (currentAudio && currentAudio !== audio) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
        if (currentIcon) currentIcon.textContent = '▶';
    }

    if (audio.paused) {
        audio.play();
        icon.textContent = '❚❚';
        currentAudio = audio;
        currentIcon = icon;
    } else {
        audio.pause();
        icon.textContent = '▶';
        currentAudio = null;
        currentIcon = null;
    }

    // when audio finishes, reset icon
    audio.onended = () => {
        icon.textContent = '▶';
        currentAudio = null;
        currentIcon = null;
    };
}
