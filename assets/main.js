const embedInputs = Array.from(document.getElementsByClassName("embedInput"))

embedInputs.forEach((embedInput) => embedInput.addEventListener("input", update))

function update() {

    const searchParams = []

    if (author.value.trim()) {
        searchParams.push(["author", encodeURIComponent(author.value.trim()).replace(/%20/g, "+")])
        previewAuthor.classList.remove("hidden")
        previewAuthor.textContent = author.value
    } else {
        previewAuthor.classList.add("hidden")
    }

    if (authorUrl.value.trim()) {
        searchParams.push(["authorurl", authorUrl.value.trim()])
    }

    if (provider.value.trim()) {
        searchParams.push(["provider", encodeURIComponent(provider.value.trim()).replace(/%20/g, "+")])
        previewProvider.classList.remove("hidden")
        previewProvider.textContent = provider.value
    } else {
        previewProvider.classList.add("hidden")
    }

    if (providerUrl.value.trim()) {
        searchParams.push(["providerurl", providerUrl.value.trim()])
    }

    if (title.value.trim()) {
        previewTitle.classList.remove("hidden")
        previewTitle.textContent = title.value
    } else {
        previewTitle.classList.add("hidden")
    }

    if (description.value.trim()) {
        searchParams.push(["description", encodeURIComponent(description.value.trim()).replace(/%20/g, "+")])
        previewDescription.classList.remove("hidden")
        previewDescription.textContent = description.value
    } else {
        previewDescription.classList.add("hidden")
    }

    if (color.value !== "#000000") {
        searchParams.push(["color", color.value.replace("#", "")])
        previewColor.style.backgroundColor = color.value
    } else {
        previewColor.style.backgroundColor = "#1E1F22"
    }

    if (image.value.trim()) {
        searchParams.push(["image", image.value.trim()])

        if (displayAsThumbnail.checked) {
            previewImage.classList.add("hidden")
            previewThumbnail.src = image.value.trim()
            previewThumbnail.classList.remove("hidden")
            searchParams.push(["imagetype", "thumbnail"])
        } else {
            previewThumbnail.classList.add("hidden")
            previewImage.src = image.value.trim()
            previewImage.classList.remove("hidden")
        }
    } else {
        previewImage.classList.add("hidden")
        previewThumbnail.classList.add("hidden")
    }

    if (redirect.value.trim()) {
        searchParams.push(["redirect", redirect.value.trim()])
    }

    searchParams.map(param => {
        return param.join("=")
    }).join("&")

    let finalLink
    const maskValue = mask.value.trim()
    const titlePath = title.value.trim() ? `${encodeURIComponent(title.value.trim()).replace(/%20/g, "+")}` : ""

    const queryString = searchParams.map(param => param.join("=")).join("&");

    if (maskValue) {
        if (maskValue.includes("http://") || maskValue.includes("https://")) {
            nToast("mask cannot be a url!")
            previewLink.textContent = `${location.origin}${location.pathname}${titlePath}?${queryString}`
            finalLink = `${location.origin}${location.pathname}${titlePath}?${queryString}`
        } else {
            finalLink = `[${maskValue}](${location.origin}${location.pathname}${titlePath}?${queryString})`
            previewLink.textContent = maskValue
        }
    } else {
        finalLink = `${location.origin}${location.pathname}${titlePath}` + (queryString ? `?${queryString}` : "")
        previewLink.textContent = finalLink
    }

    document.getElementById("generatedLink").innerText = finalLink

}

update()

if (location != "https://embed.jns.gg/") {
    location = location.origin
}
