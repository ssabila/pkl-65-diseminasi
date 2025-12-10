function initializeTheme() {
    // Force light mode only - dark mode disabled
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
    return 'light'
}

function getCurrentThemePreference() {
    // Always return light mode
    return 'light'
}

function getEffectiveTheme() {
    // Always return light mode
    return 'light'
}

function setTheme(preference) {
    // Force light mode only
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
}

function setupThemeListener() {
    // Theme listener disabled - light mode only
}

function getNextTheme(current) {
    switch (current) {
        case 'light':
            return 'dark'
        case 'dark':
            return 'system'
        case 'system':
            return 'light'
        default:
            return 'light'
    }
}

function getNextThemeText(current) {
    switch (current) {
        case 'light':
            return 'Dark Mode'
        case 'dark':
            return 'System Mode'
        case 'system':
            return 'Light Mode'
        default:
            return 'Light Mode'
    }
}

function getNextThemeIcon(current) {
    switch (current) {
        case 'light':
            return 'moon'
        case 'dark':
            return 'system'
        case 'system':
            return 'sun'
        default:
            return 'sun'
    }
}

function cycleTheme() {
    const currentPreference = getCurrentThemePreference()
    const nextTheme = getNextTheme(currentPreference)

    setTheme(nextTheme)

    window.dispatchEvent(
        new CustomEvent('themeChanged', {
            detail: {
                preference: nextTheme,
                effectiveTheme: getEffectiveTheme()
            }
        })
    )

    return {
        currentPreference: nextTheme,
        effectiveTheme: getEffectiveTheme(),
        nextThemeText: getNextThemeText(nextTheme),
        nextThemeIcon: getNextThemeIcon(nextTheme)
    }
}

function getCurrentThemeState() {
    const currentPreference = getCurrentThemePreference()
    const effectiveTheme = getEffectiveTheme()

    return {
        currentPreference,
        effectiveTheme,
        nextThemeText: getNextThemeText(currentPreference),
        nextThemeIcon: getNextThemeIcon(currentPreference),
        isSystem: currentPreference === 'system'
    }
}

function toggleTheme() {
    const currentPreference = getCurrentThemePreference()
    const nextTheme = currentPreference === 'dark' ? 'light' : 'dark'
    setTheme(nextTheme)

    return {
        currentPreference: nextTheme,
        effectiveTheme: nextTheme
    }
}

function resetToSystemTheme() {
    setTheme('system')

    return {
        currentPreference: 'system',
        effectiveTheme: getEffectiveTheme()
    }
}

initializeTheme()
setupThemeListener()

export {
    initializeTheme,
    getCurrentThemePreference,
    getEffectiveTheme,
    setTheme,
    toggleTheme,
    resetToSystemTheme,
    cycleTheme,
    getCurrentThemeState
}
