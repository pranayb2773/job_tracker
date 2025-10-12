function pluralize(string, count, replacements = {}) {
    // Determine which part of the string to use (singular or plural)
    // It splits 'singular|plural' and chooses the correct one.
    let chosenString =
        count === 1 ? string.split('|')[0] : string.split('|')[1];

    // If no plural form was provided, default to the singular form
    if (typeof chosenString === 'undefined') {
        chosenString = string.split('|')[0];
    }

    // Replace any placeholders like :count with their actual values
    for (const key in replacements) {
        chosenString = chosenString.replace(`:${key}`, replacements[key]);
    }

    return chosenString;
}

window.pluralize = pluralize;
