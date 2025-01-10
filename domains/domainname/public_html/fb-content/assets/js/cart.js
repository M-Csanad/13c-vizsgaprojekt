const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);
class Cart {
    isOpen = false;
    ease = "power3.inOut";

    // A töréspont felett az animáció máshogy működik
    breakpoint = 950;

    add() {

    }

    remove() {

    }

    changeCount() {

    }

    uploadToDB() {

    }

    writeToCookie() {

    }

    loadFromCookie() {

    }
}

export default Cart;