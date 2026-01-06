
class CaseController {

    // Default settings
    cfg = {
        animation: 'cubic-bezier(0.3, 0.6, 0.3, 1)',
        animationTime: 8000,
    };

    // Items settings
    items = [];
    itemsTemplate = '<div></div>';

    // Events
    events = {
        onAnimationStart: [],
        onAnimationEnd: [],
    };

    constructor (id, cfg) {

        // Init containers
        this.container = document.getElementById(id)
        this.itemsContainer = this.container.querySelector('[data-case-items]');
        
        // Merge configs
        this.cfg = Object.assign(this.cfg, cfg);

    }

    run(id) {
        let itemTemplate = this.getItemTemplate(id);

        if(!itemTemplate) return;

        this.clearItemsContainer();
        this.appendItemsToContainer(40);
        this.appendToContainer(itemTemplate);
        let winningItem = this.itemsContainer.children.item(this.itemsContainer.children.length - 1);
        this.appendItemsToContainer(10);

        // Animation
        let winningItemWidth = winningItem.getBoundingClientRect().width;
        let winningItemPosX = winningItem.getBoundingClientRect().x;
        let markerPosX = this.container.getBoundingClientRect().x + this.container.getBoundingClientRect().width / 2;
        
        let translateX = winningItemPosX - markerPosX + this.getRandomInt(10, winningItemWidth - 10);
        
        this.itemsContainer.style.transition = 'transform '+(this.cfg.animationTime / 1000)+'s ' + this.cfg.animation;
        this.itemsContainer.style.transform = 'translateX(-'+(translateX)+'px)';

        this.itemsContainer.ontransitionstart = () => {
            this.dispatchEvents(this.events.onAnimationStart, {
                item: this.getItem(id),
            });
        };

        this.itemsContainer.ontransitionend = () => {
            this.dispatchEvents(this.events.onAnimationEnd, {
                item: this.getItem(id),
            });
        };
    }

    setItems(items) {
        this.items = items;
        this.appendItemsToContainer(40);
    }

    setItemsTemplate(template) {
        this.itemsTemplate = template;
    }

    getRandomInt(min, max) {
        return Math.round(min - 0.5 + Math.random() * (max - min + 1));
    }

    getItem(id) {
        let result = null;
        Array.prototype.forEach.call(this.items, (item) => {
            if(item.id == id) return result = item;
        });
        return result;
    }

    getRandomItem() {
        if(this.items.length <= 0) return;
        return this.items[this.getRandomInt(0, this.items.length - 1)];
    }

    getItemTemplate(id) {
        let item = this.getItem(id);

        if(!item) return null;

        let result = this.itemsTemplate;
        let keys = Object.keys(item.context);

        Array.prototype.forEach.call(keys, (key) => {
            result = result.replaceAll('{'+key+'}', item.context[key]);
        });

        return result;     
    }

    appendItemsToContainer(count){
        if(this.items.length <= 0) return;
        let content = '';
        for(let i = 0; i < count; i++)
        {
            let itemTempate = this.getItemTemplate(this.getRandomItem().id);
            if(itemTempate) content += itemTempate;
        }
        this.appendToContainer(content);
    }

    appendToContainer(content) {
        this.itemsContainer.insertAdjacentHTML('beforeend', content);
    }

    clearItemsContainer() {
        this.itemsContainer.style.transition = null;
        this.itemsContainer.style.transform = null;
        this.itemsContainer.innerHTML = null;
    }

    onAnimationStart(func){
        this.events.onAnimationStart.push(func);
    }

    onAnimationEnd(func){
        this.events.onAnimationEnd.push(func);
    }

    dispatchEvents(events, context){
        Array.prototype.forEach.call(events, (event) => {
            event(context);
        });
    }
}
