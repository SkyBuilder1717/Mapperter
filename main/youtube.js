const apiKey = 'AIzaSyA7WUZbu_v7ralkZn7e9EujSsTYFtBPigA';
const channelId = 'UCjeUmB52ZNTG4VEL_Ze4a8g';

async function getLatestVideoId(channelId) {
    const url = `https://www.googleapis.com/youtube/v3/search?key=${apiKey}&channelId=${channelId}&order=date&part=snippet&type=video&maxResults=1`;

    try {
        const response = await fetch(url);
        if (!response.ok) {
            const errorData = await response.json();
            console.error(`HTTP error! Status: ${response.status}, Message: ${errorData.error.message}`);
        }

        const data = await response.json();

        if (data.items && data.items.length > 0) {
            return data.items[0].id.videoId;
        } else {
            throw new Error('No videos found.');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        return null;
    }
}

async function displayLatestVideo() {
    const videoId = await getLatestVideoId(channelId);
    const videoContainer = document.getElementById('video');
    if (videoId) {
        const iframe = document.createElement('iframe');
        iframe.src = `https://www.youtube.com/embed/${videoId}`;
        iframe.frameBorder = '0';
        iframe.width = '560';
        iframe.height = '315';
        iframe.allowFullscreen = true;
        videoContainer.innerHTML = '';
        videoContainer.appendChild(iframe);
    } else {
        videoContainer.innerHTML = '<p>Не удалось загрузить последнее видео.</p>';
        console.warn(status);
    }
}

document.addEventListener('DOMContentLoaded', displayLatestVideo);