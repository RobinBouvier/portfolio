document.addEventListener('DOMContentLoaded', () => {
    const numBubbles = 20;
    const body = document.body;

    for (let i = 0; i < numBubbles; i++) {
        const bubble = document.createElement('div');
        bubble.classList.add('bubble');
        bubble.style.width = `${Math.random() * 100 + 50}px`;
        bubble.style.height = bubble.style.width;
        bubble.style.top = `${Math.random() * 100}vh`;
        bubble.style.left = `${Math.random() * 100}vw`;
        body.appendChild(bubble);
    }

    document.addEventListener('mousemove', (e) => {
        const { clientX, clientY } = e;
        const { innerWidth, innerHeight } = window;
        const x = (clientX / innerWidth) * 100;
        const y = (clientY / innerHeight) * 100;

        document.querySelectorAll('.bubble').forEach(bubble => {
            const moveX = (x - 50) * 0.5;
            const moveY = (y - 50) * 0.5;
            bubble.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    });
});
