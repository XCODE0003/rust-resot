class Lightbox {
    constructor(element) {
        for (let img of element.querySelectorAll("img")) {
            if (!img.complete) {
                img.onload = () => this.BindImage(img);
            }
            else {
                this.BindImage(img);
            }
        }
        for (let video of element.querySelectorAll("video")) {
            this.BindVideo(video);
        }
    }
    BindImage(img) {
        if ((img.naturalWidth - img.width) < 30 && (img.naturalHeight - img.height) < 30)
            return;
        if (img.parentElement.tagName == "A" && img.parentElement.href != img.src)
            return;
        img.classList.add("lightbox-thumbnail");
        img.onclick = e => {
            e.preventDefault();
            this.ViewImage(img);
        };
    }
    BindVideo(video) {
        if (!video.autoplay)
            return;
        video.classList.add("lightbox-thumbnail");
        video.onclick = e => {
            e.preventDefault();
            this.ViewVideo(video);
        };
    }
    ViewImage(img) {
        Lightbox.DeleteViewElement();
        Lightbox.DragX = 0;
        Lightbox.DragY = 0;
        Lightbox.Zoom = 1;
        Lightbox.ViewingElement.style.left = 0 + "px";
        Lightbox.ViewingElement.style.top = 0 + "px";
        Lightbox.ViewingElement.style.transform = null;
        Lightbox.ViewingElement.style.transition = null;
        Lightbox.OverlayImage.classList.add("hidden");
        Lightbox.OverlayImage.src = img.src;
        Lightbox.OverlayImage.onload = () => {
            Lightbox.Overlay.classList.remove("hidden");
            setTimeout(() => Lightbox.OverlayImage.classList.remove("hidden"), 10);
        };
    }
    ViewVideo(img) {
        Lightbox.DeleteViewElement();
        Lightbox.DragX = 0;
        Lightbox.DragY = 0;
        Lightbox.Zoom = 1;
        Lightbox.ViewingElement.style.left = 0 + "px";
        Lightbox.ViewingElement.style.top = 0 + "px";
        Lightbox.ViewingElement.style.transform = null;
        Lightbox.ViewingElement.style.transition = null;
        Lightbox.ClonedElement = img.cloneNode(true);
        Lightbox.ClonedElement.classList.remove("lightbox-thumbnail");
        Lightbox.ClonedElement.style.pointerEvents = "none";
        Lightbox.ViewingElement.appendChild(Lightbox.ClonedElement);
        Lightbox.Overlay.classList.remove("hidden");
    }
    static DeleteViewElement() {
        if (Lightbox.ClonedElement) {
            Lightbox.ClonedElement.remove();
            Lightbox.ClonedElement = null;
        }
        Lightbox.OverlayImage.classList.add("hidden");
    }
    static Bind(element) {
        if (Lightbox.Overlay == null) {
            Lightbox.Overlay = document.createElement("div");
            Lightbox.Overlay.classList.add("hidden");
            Lightbox.Overlay.id = "lightbox";
            Lightbox.Overlay.onclick = () => Lightbox.Overlay.classList.add("hidden");
            Lightbox.Overlay.style.touchAction = "none";
            Lightbox.ViewingElement = document.createElement("div");
            Lightbox.ViewingElement.draggable = false;
            Lightbox.ViewingElement.style.position = "relative";
            Lightbox.OverlayImage = document.createElement("img");
            Lightbox.OverlayImage.draggable = false;
            var dragging = false;
            var dragged = false;
            var touchstart = null;
            Lightbox.ViewingElement.onmousedown = e => { dragging = true; dragged = false; e.preventDefault(); e.stopPropagation(); };
            Lightbox.ViewingElement.ontouchstart = e => { dragging = true; dragged = false; touchstart = e.targetTouches[0]; e.preventDefault(); e.stopPropagation(); };
            Lightbox.ViewingElement.onmouseup = e => { dragging = false; e.preventDefault(); e.stopPropagation(); };
            Lightbox.ViewingElement.onclick = e => { if (dragged) {
                e.preventDefault();
                e.stopPropagation();
            } };
            Lightbox.ViewingElement.append(Lightbox.OverlayImage);
            Lightbox.Overlay.append(Lightbox.ViewingElement);
            document.addEventListener("mousemove", e => {
                if (!dragging)
                    return;
                if (Lightbox.Overlay.classList.contains("hidden"))
                    return;
                Lightbox.DragX += e.movementX;
                Lightbox.DragY += e.movementY;
                Lightbox.ViewingElement.style.left = Lightbox.DragX + "px";
                Lightbox.ViewingElement.style.top = Lightbox.DragY + "px";
                dragged = true;
            });
            document.addEventListener("touchmove", e => {
                if (!dragging)
                    return;
                if (Lightbox.Overlay.classList.contains("hidden"))
                    return;
                if (e.targetTouches.length == 1) {
                    Lightbox.DragX += e.targetTouches[0].clientX - touchstart.clientX;
                    Lightbox.DragY += e.targetTouches[0].clientY - touchstart.clientY;
                    if (Lightbox.DragX != 0 || Lightbox.DragY != 0) {
                        Lightbox.ViewingElement.style.left = Lightbox.DragX + "px";
                        Lightbox.ViewingElement.style.top = Lightbox.DragY + "px";
                        touchstart = e.targetTouches[0];
                        dragged = true;
                    }
                }
                e.preventDefault();
                e.stopPropagation();
            });
            Lightbox.ViewingElement.ontouchend = e => {
                if (!dragged) {
                    Lightbox.Overlay.classList.add("hidden");
                    Lightbox.DeleteViewElement();
                }
                dragging = false;
                e.preventDefault();
                e.stopPropagation();
            };
            document.addEventListener("keydown", e => {
                if (e.key != "Escape")
                    return;
                if (Lightbox.Overlay.classList.contains("hidden"))
                    return;
                Lightbox.Overlay.classList.add("hidden");
                e.preventDefault();
            }, { passive: false });
            document.addEventListener("wheel", e => {
                if (Lightbox.Overlay.classList.contains("hidden"))
                    return;
                e.preventDefault();
                e.stopPropagation();
                Lightbox.Zoom -= e.deltaY * 0.0015 * Lightbox.Zoom;
                if (Lightbox.Zoom < 0.5)
                    Lightbox.Zoom = 0.5;
                if (Lightbox.Zoom > 5)
                    Lightbox.Zoom = 5;
                Lightbox.ViewingElement.style.transition = "transform 0.1s ease-out";
                Lightbox.ViewingElement.style.transform = "scale( " + Lightbox.Zoom + ")";
            }, { passive: false });
            document.body.append(Lightbox.Overlay);
        }
        for (var e of element.querySelectorAll("[using-lightbox]")) {
            new Lightbox(e);
        }
    }
}
Lightbox.DragX = 0;
Lightbox.DragY = 0;
Lightbox.Zoom = 1;
document.addEventListener("DOMContentLoaded", () => Lightbox.Bind(document.body));