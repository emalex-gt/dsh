<script>
    (() => {
        const refreshUrl = @js(route('session.refresh'));
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        let pendingRefresh = null;

        const syncToken = (token) => {
            if (!token) {
                return;
            }

            if (csrfMeta) {
                csrfMeta.setAttribute('content', token);
            }

            document.querySelectorAll('input[name="_token"]').forEach((input) => {
                input.value = token;
            });
        };

        const refreshSession = async () => {
            if (pendingRefresh) {
                return pendingRefresh;
            }

            pendingRefresh = fetch(refreshUrl, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache',
                },
                cache: 'no-store',
            })
                .then(async (response) => {
                    if (!response.ok) {
                        throw new Error(`Session refresh failed with status ${response.status}`);
                    }

                    return response.json();
                })
                .then((payload) => {
                    syncToken(payload.token ?? null);

                    return payload;
                })
                .finally(() => {
                    pendingRefresh = null;
                });

            return pendingRefresh;
        };

        document.addEventListener('submit', async (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if ((form.method || '').toUpperCase() !== 'POST') {
                return;
            }

            if (form.dataset.noSessionRefresh === 'true') {
                return;
            }

            const action = new URL(form.getAttribute('action') || window.location.href, window.location.origin);

            if (action.origin !== window.location.origin) {
                return;
            }

            event.preventDefault();

            try {
                await refreshSession();
            } catch (error) {
                console.warn('No se pudo refrescar la sesion antes de enviar el formulario.', error);
            }

            form.submit();
        }, true);

        window.setInterval(() => {
            if (document.visibilityState !== 'visible') {
                return;
            }

            refreshSession().catch(() => {});
        }, 5 * 60 * 1000);
    })();
</script>
