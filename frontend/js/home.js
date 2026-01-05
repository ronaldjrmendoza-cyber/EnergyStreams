/* for the live audio player and showing the program currently on air */
document.addEventListener("DOMContentLoaded", () => {
    const audio = document.getElementById("liveAudio");
    const btn   = document.getElementById("liveBtn");
    const title = btn.querySelector(".listen-title");
    let isPlaying = false;

    function setPlayState() {
        title.innerHTML = `<img src="frontend/images/play_button.svg" class="play" alt=""> LISTEN LIVE HERE <img src="frontend/images/listen_live.svg" alt="">`;
    }
        
    function setPauseState() {
        title.innerHTML = `<img src="frontend/images/pause_button.svg" class="pause" alt=""> PAUSE LIVE STREAM <img src="frontend/images/listen_live.svg" alt="">`;
    }

    btn.addEventListener("click", () => {
        if (!isPlaying) { audio.play(); isPlaying = true; setPauseState(); }
        else { audio.pause(); isPlaying = false; setPlayState(); }
    });
    setPlayState();

    // fetches data
    Promise.all([
        fetch('http://localhost:8000/backend/fetch.php?table=Program').then(r => r.json()),
        fetch('http://localhost:8000/backend/fetch.php?table=Program_Day_Type').then(r => r.json()),
        fetch('http://localhost:8000/backend/fetch.php?table=Day_Type').then(r => r.json()),
        fetch('http://localhost:8000/backend/fetch.php?table=Program_Anchor_Assignment').then(r => r.json()),
        fetch('http://localhost:8000/backend/fetch.php?table=DJ_Profile').then(r => r.json())
    ]).then(([programs, programDayTypes, dayTypes, assignments, djs]) => {
        const subtitle = document.querySelector(".listen-subtitle");
        const container = document.querySelector(".programs");
        container.innerHTML = "";

        const now = new Date();
        const day = now.getDay();
        const nowSec = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();
        const map = { WEEKDAYS: [1,2,3,4,5], SAT: [6], SUN: [0] };

        const formatToSec = t => { const [h,m,s] = t.split(':').map(Number);
            return h*3600 + m*60 + (s||0);
        };

        const getDays = id => programDayTypes.filter(p => p.PROGRAM_ID == id)
                .map(p => dayTypes.find(d => d.ID == p.DAY_TYPE_ID)?.DAY_TYPE)
                .filter(Boolean);

        const getHosts = id => assignments .filter(a => a.PROGRAM_ID == id)
                .map(a => {const dj = djs.find(d => d.ID == a.DJ_ID);
                    return dj?.STAGE_NAME || dj?.REAL_NAME?.toUpperCase();
                })
                .filter(Boolean)
                .join(", ");

        const isNowOnAir = program => {
            const start = formatToSec(program.START_TIME);
            const end = formatToSec(program.END_TIME);
            const days = getDays(program.ID);
            const fitsDay = days.some(d => map[d]?.includes(day));
            const withinTime = start < end ? nowSec >= start && nowSec <= end : nowSec >= start || nowSec <= end;
            return fitsDay && withinTime;
        };

        const onAir = programs.find(p => isNowOnAir(p));
        if (onAir) subtitle.textContent = `On Air: ${onAir.TITLE}`;
        else {
            const upcoming = programs.filter(p => getDays(p.ID).some(d => map[d]?.includes(day)))
                .sort((a,b) => formatToSec(a.START_TIME) - formatToSec(b.START_TIME))
                .find(p => formatToSec(p.START_TIME) > nowSec);
            subtitle.textContent = upcoming ? `Next: ${upcoming.TITLE} at ${upcoming.START_TIME}` : "No Active Program";
        }

        // for featured programs
        programs.map(p => ({...p,
                hosts: getHosts(p.ID)
            }))
            .filter(p => p.hosts.length > 0) // only programs with hosts
            .slice(0, 6) // limit to 6 programs
            .forEach(program => {
                const card = document.createElement("div");
                card.className = "program-card-home";
                const days = getDays(program.ID).join(", ");

                card.innerHTML = `
                    <div class="program-header">${program.TITLE} | ${days}</div>
                    <div>Time: ${program.START_TIME} – ${program.END_TIME}</div>                        
                    <div>Hosts: ${program.hosts}</div> `;

            card.addEventListener("click", () => { window.location.href = "programs.php";
        });

            container.appendChild(card);
        });

    }).catch(err => console.error(err));
});
