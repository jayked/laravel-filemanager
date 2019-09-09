class LFMComponent {
	constructor(element) {
		// Set a hash and attach it to the element so we
		// can resolve our element every time through the hash
		this.elementHash = Math.random().toString(36).substring(7);
		element.classList.add('filemanager-' + this.elementHash);

		this.init();
	}

	getElement() {
		return window.document.querySelector('.filemanager-' + this.elementHash);
        }

	/**
	 * Attach event listeners
	 */
	init() {
		this.getElement().addEventListener('click', () => this.open());
	}

	/**
	 * Detach event listeners
	 */
	destroy() {
		this.getElement().removeEventListener('click', () => this.open());
    }

	/**
	 * Open the filemanager window
	 */
	open() {
		localStorage.setItem('target_input', this.getElement().dataset.filemanagerInput);
		localStorage.setItem('target_preview', this.getElement().dataset.filemanagerPreview);
		window.open('/laravel-filemanager?type=' + this.resolveType(), 'FileManager', 'width=900,height=900');
	}

	/**
	 * Resolve type from dataset and resolve
	 * final type with "Files" as default
	 *
	 * @returns {string}
	 */
	resolveType() {
		let dataType = this.getElement().dataset.filemanagerType;
		if(dataType === 'image' || dataType === 'images') {
			return 'Images';
		}

		return 'Files';
	}
}

class LFM {
	constructor() {
		// Create an array in which we will track all components
		this.components = [];
		this.init();
	}

	/**
	 * Initialize all components
	 */
	init() {
		let elements = window.document.querySelectorAll('.filemanager');
		for(let index = 0; index < elements.length; index++) {
			this.addElement(elements[index]);
		}
	}

	/**
	 * Create a new component from an element
	 *
	 * @param element
	 */
	addElement(element) {
		this.components.push(new LFMComponent(element));
	}

	/**
	 * Destroy all components
	 */
	destroy() {
		for(let index = 0; index < this.components.length; index++) {
			this.components[index].destroy();
			this.components.splice(index);
		}
	}
}

function SetUrl(url){
	// Set the value of the desired input to image url
	let targetInput = window.document.querySelector('#' + localStorage.getItem('target_input'));
	targetInput.value = url;

	// Set or change the preview image src
	let targetPreview = window.document.querySelector('#' + localStorage.getItem('target_preview'));
	targetPreview.setAttribute('src',url);
}
