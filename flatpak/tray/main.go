// Command phpgui-tray starts the bundled PHP web server, shows a tray icon and stops
// the server again when the user chooses Quit.
package main

import (
	_ "embed"
	"fmt"
	"net"
	"net/http"
	"net/url"
	"os"
	"os/exec"
	"os/signal"
	"syscall"
	"time"

	"fyne.io/systray"
)

//go:embed tray.png
var trayPNG []byte

const appRoot = "/app/share/phpgui"

func main() {
	port := os.Getenv("PHPGUI_PORT")
	if port == "" {
		port = "8088"
	}
	// PHPGUI_BIND=0.0.0.0 makes the interface reachable from the network (default: local only).
	bind := os.Getenv("PHPGUI_BIND")
	if bind == "" {
		bind = "127.0.0.1"
	}
	base := "http://127.0.0.1:" + port + "/"
	target := base
	if len(os.Args) > 1 && len(os.Args[1]) > 0 {
		target = base + "index.php?ajfsp_link=" + url.QueryEscape(os.Args[1])
	}

	// Second start: the server already runs, only open the browser.
	if alive(base + "index.html") {
		open(target)
		return
	}

	os.Setenv("TOP_SHOW_PERMALINK", "0")
	if os.Getenv("CORE_HOST") == "" {
		os.Setenv("CORE_HOST", "http://127.0.0.1")
	}
	cmd := exec.Command("php", "-d", "variables_order=EGPCS", "-d", "expose_php=0",
		"-d", "memory_limit=256M", "-d", "session.save_path="+os.Getenv("XDG_CACHE_HOME"),
		"-S", net.JoinHostPort(bind, port), "-t", appRoot+"/public")
	cmd.Env = append(os.Environ(), "PHP_CLI_SERVER_WORKERS=4")
	cmd.SysProcAttr = &syscall.SysProcAttr{Pdeathsig: syscall.SIGKILL}
	if err := cmd.Start(); err != nil {
		fmt.Fprintln(os.Stderr, "cannot start php:", err)
		os.Exit(1)
	}
	exited := make(chan struct{})
	go func() { cmd.Wait(); close(exited) }()

	for i := 0; i < 50 && !alive(base+"index.html"); i++ {
		time.Sleep(100 * time.Millisecond)
	}
	open(target)

	stop := func() { cmd.Process.Kill() }
	sig := make(chan os.Signal, 1)
	signal.Notify(sig, syscall.SIGINT, syscall.SIGTERM)
	go func() { <-sig; stop(); systray.Quit() }()
	go func() { <-exited; systray.Quit() }()

	// Without a tray host systray.Run returns or panics; keep serving until the server ends.
	func() {
		defer func() { recover() }()
		systray.Run(func() {
			systray.SetIcon(trayPNG)
			systray.SetTitle("appleJuice phpGUI")
			systray.SetTooltip("appleJuice phpGUI " + base)
			openItem := systray.AddMenuItem("Open web interface", "")
			systray.AddSeparator()
			quit := systray.AddMenuItem("Quit", "")
			go func() {
				for {
					select {
					case <-openItem.ClickedCh:
						open(base)
					case <-quit.ClickedCh:
						stop()
						systray.Quit()
						return
					}
				}
			}()
		}, nil)
	}()
	// Tray ended by Quit (server killed) or no tray host: keep serving until the server ends.
	if _, err := net.DialTimeout("tcp", "127.0.0.1:"+port, 200*time.Millisecond); err == nil {
		<-exited
	}
}

func alive(u string) bool {
	c := http.Client{Timeout: 500 * time.Millisecond}
	r, err := c.Get(u)
	if err != nil {
		return false
	}
	r.Body.Close()
	return r.StatusCode < 500
}

func open(u string) { exec.Command("xdg-open", u).Start() }
