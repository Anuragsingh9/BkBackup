const routeAgent = {
    routes: {
        QUICK_REGISTER: event_uuid => `/quick-register/${event_uuid}`,
        EVENT_DASHBOARD: event_uuid => `/dashboard/${event_uuid}`,
        EVENT_LIST: () => `/event-list`,
        QUICK_LOGIN: (event_uuid = null) => `/quick-login/${event_uuid ? `/${event_uuid}` : ''}`,
    },
}

export default routeAgent;