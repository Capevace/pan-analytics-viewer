@props([
    'events' => null,
    'forceAll' => false,
    'toggle' => true,
])

@styles
<style>
    .pan-widget-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        font-family: monospace;
        margin-bottom: 0.5rem;
    }

    .pan-widget-content {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        align-items: center;
        justify-content: space-between;
        color: #94a3b8;
    }

    .pan-widget-content-item span {
        font-family: monospace;
        font-size: 1.6rem;
        color: #fff;
    }

    .pan-toggle {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        background-color: #334155;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        transition: opacity 0.2s ease-in-out;
    }

    .pan-toggle.inactive {
        opacity: 0.5;
    }

    .pan-toggle:hover {
        opacity: 1;
    }
</style>
<style id="pan-dynamic-styles">

</style>

@endstyles

@script
{{-- Tippy.js --}}
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script type="module">
    /**
     * @typedef {Object} Analytic
     * @property {int} id
     * @property {string} name
     * @property {int} impressions
     * @property {int} hovers
     * @property {int} clicks
     */

    /**
     * @param events {string[]}
     * @return {Promise<Analytic[]>}
     */
    function fetchPanData(events) {
        @if ($forceAll)
        events = [];
        @endif

        const url = '{!! \Illuminate\Support\Facades\URL::temporarySignedRoute('pan-analytics-viewer.endpoint', expiration: now()->addHour()) !!}';

        return fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Pan-Events': events ? events.join(',') : null
            }
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Pan response was not ok');
                }

                return response.json();
            })
            .then((data) => {
                return data.analytics;
            });
    }

    function buildPanWidget(analytic) {
        const html = `
        <div class="pan-widget">
            <div class="pan-widget-header">
                <h3>${analytic.name}</h3>
            </div>
            <div class="pan-widget-content">
                <div class="pan-widget-content-item">
                    <h4>Impressions</h4>
                    <span>${analytic.impressions}</span>
                </div>
                <div class="pan-widget-content-item">
                    <h4>Hovers</h4>
                    <span>${analytic.hovers}</span>
                </div>
                <div class="pan-widget-content-item">
                    <h4>Clicks</h4>
                    <span>${analytic.clicks}</span>
                </div>
            </div>
        </div>
        `;

        return html;
    }

    async function buildPanToggle() {
        let destroyViewer = await initPanViewer();

        @if ($toggle)
        const button = document.createElement('button');
        button.textContent = 'Hide Pan Analytics';
        button.classList.add('pan-toggle');
        button.classList.add('inactive');

        button.addEventListener('click', async () => {
            if (destroyViewer) {
                destroyViewer();
                destroyViewer = null;

                button.textContent = 'Show Pan Analytics';
            } else {
                destroyViewer = await initPanViewer();

                button.textContent = 'Hide Pan Analytics';
            }
        });

        document.body.appendChild(button);
        @endif

        return () => {
            @if ($toggle)
            button.remove();
            @endif

            destroyViewer?.();
        }
    }

    async function initPanViewer() {
        const events = Array.from(document.querySelectorAll('[data-pan]'))
            .map((element) => element.dataset.pan);

        const analytics = await fetchPanData(events);

        const viewers = [];

        for (const analytic of analytics) {
            const content = buildPanWidget(analytic);

            const parent = document.querySelector(`[data-pan="${analytic.name}"]`);

            if (!parent) {
                continue;
            }

            const popup = tippy(parent, {
                content: content,
                allowHTML: true,
                maxWidth: 500,
                followCursor: true,
                theme: 'translucent'
            });

            viewers.push(popup);
        }

        return () => {
            for (const popup of viewers) {
                popup?.destroy();
            }
        }
    }



    window.initPanViewer = initPanViewer;

    let destroyViewer = null;

    window.pan = {
        async show() {
            destroyViewer = await buildPanToggle();
        },
        hide() {
            if (destroyViewer) {
                destroyViewer();
                destroyViewer = null;
            }
        }
    };

    window.pan.show();
</script>
@endscript
